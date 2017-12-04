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
    * Classe de Regra de Negócio para Autenticação
    * Data de Criação   : 26/12/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Regra

    $Id: RTesourariaAutenticacao.class.php 64368 2016-01-28 12:04:02Z franver $

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.15, uc-02.04.04, uc-02.04.05, uc-02.04.09,uc-02.04.20
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php"                               );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoConfiguracao.class.php"                            );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                                             );
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php");

/**
    * Classe de Regra de Autenticação
    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class RTesourariaAutenticacao
{
/*
    * @var Object
    * @access Private
*/
var $obRTesourariaConfiguracao;
/*
    * @var Object
    * @access Private
*/
var $obRAdministracaoUF;
/*
    * @var String
    * @access Private
*/
var $inCodAutenticacao;
/*
    * @var String
    * @access Private
*/
var $stDataAutenticacao;
/*
    * @var String
    * @access Private
*/
var $stTipo;
/*
    * @var String
    * @access Private
*/
var $stDescricao;

/*
    * @access Public
    * @param String $valor
*/
function setCodAutenticacao($valor) { $this->inCodAutenticacao           = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDataAutenticacao($valor) { $this->stDataAutenticacao          = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTipo($valor) { $this->stTipo                      = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao                 = $valor; }

/*
    * @access Public
    * @return String
*/
function getCodAutenticacao() { return $this->inCodAutenticacao;            }
/*
    * @access Public
    * @return String
*/
function getDataAutenticacao() { return $this->stDataAutenticacao;           }
/*
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo;                       }
/*
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;                  }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaAutenticacao()
{
    $this->obRTesourariaConfiguracao    = new RTesourariaConfiguracao();
    $this->obRAdministracaoConfiguracao = new RAdministracaoConfiguracao();
}

/**
    * Executa um proximoCodigo na Persistente
    * @access Public
    * @param Object $boTransacao
    * @return Object Objeto Erro
*/
function buscaProximoCodigo($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaAutenticacao.class.php"           );
    $obTTesourariaAutenticacao = new TTesourariaAutenticacao;

    $this->obRTesourariaConfiguracao->setCodModulo(30);
    $obErro = $this->obRTesourariaConfiguracao->consultarTesouraria( $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaAutenticacao->setDado( 'numeracao_comprovacao', $this->obRTesourariaConfiguracao->getNumeracaoComprovacao());
        $obTTesourariaAutenticacao->setDado( 'reiniciar_comprovacao', $this->obRTesourariaConfiguracao->getReiniciarNumeracao()  );
        $obTTesourariaAutenticacao->setDado( 'dt_autenticacao'      , $this->stDataAutenticacao                                  );
        $obErro = $obTTesourariaAutenticacao->proximoCodigo( $inCodAutenticacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setCodAutenticacao($inCodAutenticacao);
        }
    }

    return $obErro;
}

function consultarMovimentacao(&$boMovimentacao, $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaAutenticacao.class.php"           );
    $obTTesourariaAutenticacao = new TTesourariaAutenticacao;

    $stFiltro = "
        WHERE
            dt_autenticacao BETWEEN
                to_date('01/01/".$this->getDataAutenticacao()."','dd/mm/yyyy')  AND
                to_date('31/12/".$this->getDataAutenticacao()."','dd/mm/yyyy')
    ";

    $obTTesourariaAutenticacao->setDado( 'filtro', $stFiltro);
    $obErro = $obTTesourariaAutenticacao->consultaMovimentacao( $boMovimentacao, $boTransacao );

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function autenticar($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaAutenticacao.class.php"           );
    $obTransacao                         = new Transacao();
    $obTTesourariaAutenticacao           = new TTesourariaAutenticacao();
    $boFlagTransacao = false;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->buscaProximoCodigo( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTTesourariaAutenticacao->setDado( 'cod_autenticacao'        , $this->getCodAutenticacao()    );
            $obTTesourariaAutenticacao->setDado( 'dt_autenticacao'         , $this->getDataAutenticacao()   );
            $obTTesourariaAutenticacao->setDado( 'tipo'                    , $this->getTipo()               );
            $obErro = $obTTesourariaAutenticacao->inclusao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaAutenticacao );

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function montaComprovante(&$cabecalho, &$rodape, $boTransacao = "")
{
    $obTransacao = new Transacao();
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRTesourariaConfiguracao->setCodModulo( 2 );
        $this->obRTesourariaConfiguracao->setParametro( null );
        $this->obRTesourariaConfiguracao->setValor( null );
        $obErro = $this->obRTesourariaConfiguracao->listar( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            while (!$rsRecordSet->eof()) {
                if($rsRecordSet->getCampo("parametro")=="nom_prefeitura")
                    $nom_prefeitura = $rsRecordSet->getCampo("valor");

                if($rsRecordSet->getCampo("parametro")=="tipo_logradouro")
                    $logradouro = $rsRecordSet->getCampo("valor");
                if($rsRecordSet->getCampo("parametro")=="logradouro")
                    $logradouro .= " ".$rsRecordSet->getCampo("valor");
                if($rsRecordSet->getCampo("parametro")=="numero")
                    $logradouro .= ", ".$rsRecordSet->getCampo("valor");
                if($rsRecordSet->getCampo("parametro")=="complemento")
                    $logradouro .= " ".$rsRecordSet->getCampo("valor");

                $vlMascValor = $rsRecordSet->getCampo("valor");

                if ($rsRecordSet->getCampo("parametro")=="cep") {
                    $obMascara = new Mascara;
                    $obMascara->setMascara('99999-999');
                    $obMascara->mascaraDado($vlMascValor);
                    $rsRecordSet->setCampo("valor",$vlMascValor);
                    $cep = $obMascara->getMascarado();
                }

                if ($rsRecordSet->getCampo("parametro")=="fone") {
                    $obMascara = new Mascara;
                    $obMascara->setMascara('(99)9999-9999');
                    $obMascara->mascaraDado($vlMascValor);
                    $rsRecordSet->setCampo("valor",$vlMascValor);
                    $fone = $obMascara->getMascarado();
                }

                if ($rsRecordSet->getCampo("parametro")=="cnpj") {
                    $obMascara = new Mascara;
                    $obMascara->setMascara('99.999.999/9999-99');
                    $obMascara->mascaraDado($vlMascValor);
                    $rsRecordSet->setCampo("valor",$vlMascValor);
                    $cnpj = $obMascara->getMascarado();
                }
                $rsRecordSet->proximo();
            }

            $obErro = $this->obRAdministracaoConfiguracao->consultarMunicipio( $boTransacao );
        }

        if ( !$obErro->ocorreu() ) {
            //MONTA CABEÇALHO
            $cabecalho = str_pad($nom_prefeitura, 60, " ", STR_PAD_BOTH)."\\n\\n";

            $cabecalho .= str_pad($logradouro, 30, " ", STR_PAD_BOTH);
            $cabecalho .= str_pad("CEP: ".$cep, 30, " ", STR_PAD_BOTH)."\\n";

            $cabecalho .= str_pad("Fone: ".$fone, 30, " ", STR_PAD_BOTH);
            $cabecalho .= str_pad($this->obRAdministracaoConfiguracao->getNomMunicipio()." - ".$this->obRAdministracaoConfiguracao->getSiglaUf(), 30, " ", STR_PAD_BOTH)."\\n";

            $cabecalho .= str_pad("CNPJ: ".$cnpj, 60, " ", STR_PAD_BOTH)."\\n\\n";

            $cabecalho .= str_pad("Data: ".date('d/m/Y'), 30, " ", STR_PAD_BOTH);
            $cabecalho .= str_pad("Hora: ".date('G:i:s'), 30, " ", STR_PAD_BOTH)."\\n";

            $cabecalho .= str_pad("Caixa: ".Sessao::getUsername(), 60, " ", STR_PAD_BOTH)."\\n\\n";

            //MONTA RODAPÉ
            $rodape = str_pad($this->obRTesourariaConfiguracao->getDigitos().str_pad($this->getCodAutenticacao(), 6, "0", STR_PAD_LEFT), 60, " ")."\\n";
            $rodape .= str_pad($this->obRAdministracaoConfiguracao->getNomMunicipio().", ".dataExtenso(date('Y-m-d')) , 60, " ",STR_PAD_LEFT)."\\n";
        }

    }

    return $obErro;
}

}
