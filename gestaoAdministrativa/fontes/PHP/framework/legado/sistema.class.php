<?php
/*
**********************************************************************************
*                                                                                *
* @package URBEM CNM - Soluções em Gestão Pública                                *
* @copyright (c) 2013 Confederação Nacional de Municípos                         *
* @author Confederação Nacional de Municípios                                    *
*                                                                                *
* O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
* os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
* Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
*                                                                                *
* Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
* SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
* ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
* para mais detalhes.                                                            *
*                                                                                *
* Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
* com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
* no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
*                                                                                *
**********************************************************************************
*/
?>
<?php

/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

class sistema
{
    /*** Declaração das variáveis da classe ***/
    var $responsavel;
    var $modulo;
    var $erro;
    var $busca;
    var $mensagem;
    var $nome;
    var $endereco;
    var $bairro;
    var $cep;
    var $ddd;
    var $fone;
    var $fax;
    var $email;
    var $cnpj;
    var $populacao;
    var $periodoAuditoria;
    var $municipio;
    var $logotipo;
    var $destinatario;
    var $corpo;
    var $assunto;
    var $funcionalidade;
    var $acao1;
    var $uf;

    /*** Método Construtor ***/
    function sistema()
    {
        $this->responsavel = "";
        $this->modulo = "";
        $this->erro = "";
        $this->busca = "";
        $this->mensagem = "";
        $this->nome = "";
        $this->endereco = "";
        $this->bairro = "";
        $this->cep = "";
        $this->ddd = "";
        $this->fone = "";
        $this->fax = "";
        $this->email = "";
        $this->cnpj = "";
        $this->populacao = "";
        $this->periodoAuditoria = "";
        $this->municipio = "";
        $this->logotipo = "";
        $this->destinatario = "suporte@cnm.org.br";
        $this->corpo = "";
        $this->assunto = "BUG - ";
        $this->funcionalidade = "";
        $this->acao1 = "";
        $this->uf = "";
    }

    /*** Método de Vizualização do Status do Sistema ***/
    static function consultaStatus()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select valor
                        from administracao.configuracao where parametro = 'status'";
        $dbConfig->abreSelecao($select);
        $status = $dbConfig->pegaCampo("valor");
        $dbConfig->limpaSelecao();

        $dbConfig->fechaBd();
        return $status;
    }

    /*** Método de alteração do Status do sistema ***/
    function alteraStatus($status)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update = " UPDATE
                        administracao.configuracao
                    SET
                        valor = '$status'
                    WHERE
                        parametro = 'status'";
        if ($dbConfig->executaSql($update)) {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

    /*** Método que seta variáveis ***/
    function setaVariaveis($usuario, $mod)
    {
        $this->responsavel = $usuario;
        $this->modulo = $mod;
    }

    /*** Método que seleciona usuários ***/
    function montaListaUser()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select username
                        from administracao.usuario where username like '%$this->busca%' order by username";
        $dbConfig->abreSelecao($select);
        while (!$dbConfig->eof()) {
            $aLista[] = $dbConfig->pegaCampo("username");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();

        $dbConfig->fechaBd();
        return $aLista;
    }

     /*** Método que seleciona modulos ***/
    function montaListaMod()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select nom_modulo
                        from administracao.modulo order by cod_modulo";
        $dbConfig->abreSelecao($select);
        while (!$dbConfig->eof()) {
            $mLista[] = $dbConfig->pegaCampo("nom_modulo");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();

        $dbConfig->fechaBd();
        return $mLista;
    }

    /*** Método que define os responsáveis pelos módulos ***/
    function defineResponsavel($codUsuario, $codModulo)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update = " UPDATE
                        administracao.modulo
                    SET
                        cod_responsavel = '$codUsuario'
                    WHERE
                        cod_modulo = '$codModulo'";
        if ($dbConfig->executaSql($update)) {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

    /*** Método que lista os responsáveis pelos módulos ***/
    function mostraResponsavel()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select u.username, m.nom_modulo
                        from administracao.usuario as u, administracao.modulo as m
                        where m.cod_responsavel = u.numcgm order by m.cod_modulo";
        $dbConfig->abreSelecao($select);
        while (!$dbConfig->eof()) {
            $lista[] = $dbConfig->pegaCampo("username")."/".$dbConfig->pegaCampo("nom_modulo");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();

        $dbConfig->fechaBd();
        return $lista;
    }

    /*** Método que Seleciona os dados da Prefeitura ***/
    function selecionaPrefeitura()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select valor
                        from administracao.configuracao where parametro = 'nom_prefeitura'";
        $dbConfig->abreSelecao($select);
        $this->prefeitura = $dbConfig->pegaCampo("valor");
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
    }

     /*** Método que Seleciona o Módulo ***/
    function selecionaModulo()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select cod_modulo, nom_modulo
                        from administracao.modulo";
        $dbConfig->abreSelecao($select);
        while (!$dbConfig->eof()) {
            $codigo = $dbConfig->pegaCampo("cod_modulo");
            $mLista[$codigo] = $dbConfig->pegaCampo("nom_modulo");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();

        $dbConfig->fechaBd();
        return $mLista;
    }

    /*** Método que Seleciona a Funcionalidade ***/
    function selecionaFuncionalidade($mod)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
            $select =   "select cod_funcionalidade, nom_funcionalidade
                            from administracao.funcionalidade
                            where cod_modulo = '$mod'";
            //echo $select."<br>";
            $dbConfig->abreSelecao($select);
            while (!$dbConfig->eof()) {
                $codigo = $dbConfig->pegaCampo("cod_funcionalidade");
                $fLista[$codigo] = $dbConfig->pegaCampo("nom_funcionalidade");
                $dbConfig->vaiProximo();
            }
            $dbConfig->limpaSelecao();

            $dbConfig->fechaBd();
            return $fLista;
    }

    /*** Método que Seleciona a Ação ***/
    function selecionaAcao($func)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select cod_acao, nom_acao
                        from administracao.acao
                        where cod_funcionalidade = '$func'";
        $dbConfig->abreSelecao($select);
        while (!$dbConfig->eof()) {
            $codigo = $dbConfig->pegaCampo("cod_acao");
            $aLista[$codigo] = $dbConfig->pegaCampo("nom_acao");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();

        $dbConfig->fechaBd();
        return $aLista;
    }

    /*** Método que solicita suporte ***/
    function solicitaSuporte()
    {
        if (mail("$this->destinatario","$this->assunto","$this->corpo","From: $this->remetente\n"))
            return true;
        else
            return false;
    
        $dbConfig->fechaBd();
    }

    /*** Método que mostra a mensagem atual do sistema ***/
    function mostraMensagem()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select = "select valor from administracao.configuracao where parametro = 'mensagem'";
        $dbConfig->abreSelecao($select);
        $msg = $dbConfig->pegaCampo("valor");
        $dbConfig->limpaSelecao();
        if ($msg) {
            return $msg;
            $dbConfig->fechaBd();
        } else {
            return $this->erro = "<b><h1>"."Mensagem não pode ser selecionada"."</b></h1>";
            $dbConfig->fechaBd();
        }
    }

    /*** Método que registra a mensagem do sistema ***/
    function registraMensagem()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update  = "update administracao.configuracao set valor = '$this->mensagem' where parametro = 'mensagem'";
        $update .= " and exercicio = '".Sessao::getExercicio()."'";
        $msg = $dbConfig->executaSql($update);
        if ($msg) {
            $dbConfig->fechaBd();
            return $this->erro = "<b><h1>"."Mensagem postada com êxito"."</b></h1>";
        } else {
            $dbConfig->fechaBd();
            return $this->erro = "<b><h1>"."Mensagem Não pode ser postada"."</b></h1>";
        }
    }

    /*** Método que seta as variáveis para edição das configurações básicas ***/
    function setaVariaveisConf($nome, $endereco, $bairro, $cep, $ddd, $fone, $fax, $email, $cnpj, $populacao, $periodoAuditoria, $uf, $municipio, $logotipo) { 
    {
        $this->nome = $nome;
        $this->endereco = $endereco;
        $this->bairro = $bairro;
        $this->cep = $cep;
        $this->ddd = $ddd;
        $this->fone = $fone;
        $this->fax = $fax;
        $this->email = $email;
        $this->cnpj = $cnpj;
        $this->populacao = $populacao;
        $this->periodoAuditoria = $periodoAuditoria;
        $this->uf = $uf;
        $this->municipio = $municipio;
        $this->logotipo = $logotipo;
    }

    /***Método que monta a lista de Estados ***/
    function montaListaUf()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select cod_uf, nom_uf
                        from ".UF;
        $dbConfig->abreSelecao($select);
        while (!$dbConfig->eof()) {
            $keycod = $dbConfig->pegaCampo("cod_uf");
            $uLista[$keycod] = $dbConfig->pegaCampo("nom_uf");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();

        $dbConfig->fechaBd();
        return $uLista;
        #return $cod;
    }

    /***Método que monta a lista de municípios ***/
    function montaListaMun($estado)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select cod_municipio, nom_municipio
                        from sw_municipio
                        where cod_uf = '$estado'";
        $dbConfig->abreSelecao($select);
        while (!$dbConfig->eof()) {
            $keycod = $dbConfig->pegaCampo("cod_municipio");
            $mLista[$keycod] = $dbConfig->pegaCampo("nom_municipio");
            $dbConfig->vaiProximo();
        }
        $dbConfig->limpaSelecao();

        return $mLista;
        #return $cod;
        #$dbConfig->fechaBd();
    }

    /*** Método que seleciona os dados básicos de configuração ***/
    function mostraConfiguracao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select =   "select c.nom_prefeitura, c.endereco, c.bairro, c.cep, c.ddd, c.fone, c.fax, c.e_mail, c.cnpj, c.populacao, c.logotipo, c.periodo_auditoria, u.nom_uf, m.nom_municipio
                        from administracao.configuracao as c, sw_municipio as m, sw_uf as u
                        where c.cod_municipio = m.cod_municipio";
        $dbConfig->abreSelecao($select);
        $this->nome = $dbConfig->pegaCampo("nom_prefeitura");
        $this->endereco = $dbConfig->pegaCampo("endereco");
        $this->bairro = $dbConfig->pegaCampo("bairro");
        $this->cep = $dbConfig->pegaCampo("cep");
        $this->ddd = $dbConfig->pegaCampo("ddd");
        $this->fone = $dbConfig->pegaCampo("fone");
        $this->fax = $dbConfig->pegaCampo("fax");
        $this->email = $dbConfig->pegaCampo("e_mail");
        $this->cnpj = $dbConfig->pegaCampo("cnpj");
        $this->populacao = $dbConfig->pegaCampo("populacao");
        $this->logotipo = $dbConfig->pegaCampo("logotipo");
        $this->periodoAuditoria = $dbConfig->pegaCampo("periodo_auditoria");
        $this->uf = $dbConfig->pegaCampo("nom_uf");
        $this->municipio = $dbConfig->pegaCampo("nom_municipio");
    }

    /*** Método que edita as configurações básicas do sistema (Nome da prefeitura, endereço, cep, email, fone, período_auditoria e municipio) ***/
    function editaConfiguracaoBasica()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update =   "update administracao.configuracao set
                        nom_prefeitura = '$this->nome',
                        endereco = '$this->endereco',
                        bairro = '$this->bairro',
                        cep = '$this->cep',
                        ddd = '$this->ddd',
                        fone = '$this->fone',
                        fax = '$this->fax',
                        e_mail = '$this->email',
                        cnpj = '$this->cnpj',
                        populacao = '$this->populacao',
                        periodo_auditoria = '$this->periodoAuditoria',
                        cod_uf = '$this->uf',
                        cod_municipio = '$this->municipio',
                        logotipo = '$this->logotipo'";
        $config = $dbConfig->executaSql($update);
        if ($config)
            return true;
        else
            return false;
    }
    }
}
?>
