<?php
class dao {

    /**
     * Função auxiliar para chamar o backend via HTTP.
     * Recebe o endpoint (ex.: "get_polo") e um array opcional de parâmetros.
     */
    public function call_backend($url, $method = 'GET', $data = null) {
        $base_url = "https://sistemascead.ufjf.br/backend/wordpress/";
    
        $ch = curl_init($base_url . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
        $headers = [];
    
        if ($method === 'POST' && $data !== null) {
            $jsonData = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($jsonData);
        }
    
        // HTTPS localhost/self-signed
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
    
        $response = curl_exec($ch);
    
        if (curl_errno($ch)) {
            error_log('cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }
    
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        if ($httpcode >= 200 && $httpcode < 300) {
            return json_decode($response, true);
        }
    
        error_log("Erro HTTP $httpcode ao chamar $url. Corpo: $response");
        return null;
    }

    public function getCursosDoPolo($nome_polo) {
        return $this->call_backend("cursos_ativos/" . rawurlencode($nome_polo) . "/");
    }

    public function getDescricaoPolo($nome_curso) {
        return $this->call_backend("polo/" . rawurlencode($nome_curso) . "/apresentacao/");
    }

    public function getInfoPolo($nome_polo) {
        return $this->call_backend("polo/" . rawurlencode($nome_polo) . "/");
    }

    public function getHorarioFuncPolo($nome_polo) {
        return $this->call_backend("polo/" . rawurlencode($nome_polo) . "/horario-funcionamento/" );
    }

    public function getPolos() {
        return $this->call_backend("polos/");
    }

    public function getPolosInfos($titulos) {
        if (empty($titulos)) return [];
        return $this->call_backend("polo/informacoes/", 'POST', ['polo' => $titulos]);
    }

    public function getPoloIds() {
        $resp = $this->call_backend("polos/ids/");
        return $resp ? $resp['polos_ids'] : [];
    }

    public function getNomePolo($nome_polo) {
        return $this->call_backend("polo/com-oferta-ativa/" . rawurlencode($nome_polo) . "/");
    }

    public function getPolosDoCurso($nome_curso) {
        return $this->call_backend("polos_ativos/" . rawurlencode($nome_curso) ."/");
    }

    public function getDescricaoCurso($nome_curso) {
        return $this->call_backend("curso/" .rawurlencode($nome_curso). "/descricao-perfil-egresso/");
    }

    public function getInfoCurso($nome_curso) {
        return $this->call_backend("curso/" .rawurlencode($nome_curso). "/contato/");
    }
}
?>
