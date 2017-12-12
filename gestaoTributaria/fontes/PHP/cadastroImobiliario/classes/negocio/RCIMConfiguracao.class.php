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
     * Classe de regra de negócio para configuração do módulo
     * Data de Criação: 30/08/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMConfiguracao.class.php 63679 2015-09-29 14:38:48Z arthur $

     * Casos de uso: uc-05.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class RCIMConfiguracao
{
/**#@+
    * @var Object
    * @access Private
*/
var $obTConfiguracao;
var $obTAcao;
var $obTransacao;
/**#@-*/

/**#@+
    * @var String
    * @access Private
*/
var $stNavegacaoAutomatico;
var $stMascaraLote;
var $stMascaraIM;
/**#@-*/

/**#@+
    * @var Integer
    * @access Private
*/
var $inNumeroIM;
var $inCodigoModulo;
var $inAnoExercicio;
/**#@-*/

/**#@+
    * @var Boolean
    * @access Private
*/
var $boCodigoLocal;
/**#@-*/

/**#@+
    * @var Array
    * @access Private
*/
var $arOrdemEntrega;
var $arOrdemMD; //Ordem Metro Dois Ou M² :)
var $arOrdemAliquota; //Ordem Aliquota
var $arAtbEdificacao;
var $arAtbImovel;
var $arAtbLoteUrbano;
var $arAtbLoteRural;

/**#@-*/

/**#@+
    * @var Object
    * @access Private
*/
var $rsRSOrdemEntrega;
var $rsRSMD;
var $rsRSAliquota;
/**#@-*/

/**
    * @access Public
    * @param Object $valor
*/
function setTConfiguracao($valor) { $this->obTConfiguracao  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setNavegacaoAutomatico($valor) { $this->stNavegacaoAutomatico    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCodigoLocal($valor) { $this->boCodigoLocal    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascaraLote($valor) { $this->stMascaraLote    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setMascaraIM($valor) { $this->stMascaraIM      = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setNumeroIM($valor) { $this->inNumeroIM       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCodigoModulo($valor) { $this->inCodigoModulo   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setAnoExercicio($valor) { $this->inAnoExercicio   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setOrdemEntrega($valor) { $this->arOrdemEntrega   = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRSOrdemEntrega($valor) { $this->rsRSOrdemEntrega = $valor; }

/**
    * @access Public
    * @return String
*/
function getNavegacaoAutomatico() { return $this->stNavegacaoAutomatico; }
/**
    * @access Public
    * @return Object
*/
function getTConfiguracao() { return $this->obTConfiguracao;  }
/**
    * @access Public
    * @return Boolean
*/
function getCodigoLocal() { return $this->boCodigoLocal; }
/**
    * @access Public
    * @return String
*/
function getMascaraLote() { return $this->stMascaraLote;    }
/**
    * @access Public
    * @return String
*/
function getMascaraIM() { return $this->stMascaraIM;      }
/**
    * @access Public
    * @return Integer
*/
function getNumeroIM() { return $this->inNumeroIM;       }
/**
    * @access Public
    * @return Integer
*/
function getCodigoModulo() { return $this->inCodigoModulo;   }
/**
    * @access Public
    * @return Object
*/
function getAnoExercicio() { return $this->inAnoExercicio;   }
/**
    * @access Public
    * @return Object
*/
function getOrdemEntrega() { return $this->arOrdemEntrega;   }
/**
    * @access Public
    * @return Object
*/
function getRSOrdemEntrega() { return $this->rsRSOrdemEntrega; }
function getRSMD() { return $this->rsRSMD; }
function getRSAliquota() { return $this->rsRSAliquota; }

/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    $this->arOrdemEntrega   = array();
    $this->arOrdemMD = array();
    $this->arOrdemAliquota = array();
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php" );
    include_once( CLA_TRANSACAO );
    $this->obTConfiguracao  = new TAdministracaoConfiguracao;
    $this->rsRSOrdemEntrega = new RecordSet;
    $this->rsRSMD           = new RecordSet;
    $this->rsRSAliquota     = new RecordSet;
    $this->obTAcao          = new TAdministracaoAcao;
    $this->obTransacao      = new Transacao;
}

function verificaParametro(&$boExiste, $boTransacao = "")
{
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsConfiguracao->Eof() )
            $boExiste = false;
        else
            $boExiste = true;
    }

    return $obErro;
}

/**
    * Altera as configurações referentes ao cadastro imobiliário
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarConfiguracao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $obErro = $this->buscaModulo( $boTransacao );
    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTConfiguracao->setDado( "cod_modulo", $this->inCodigoModulo );
    $this->obTConfiguracao->setDado( "exercicio" , $this->inAnoExercicio );

    $this->obTConfiguracao->setDado( "parametro" , "codigo_localizacao" );
    $this->obTConfiguracao->setDado( "valor"     , $this->boCodigoLocal );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }
        
    $this->obTConfiguracao->setDado( "parametro" , "numero_inscricao" );
    $this->obTConfiguracao->setDado( "valor"     , $this->inNumeroIM );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTConfiguracao->setDado( "parametro" , "mascara_lote" );
    $this->obTConfiguracao->setDado( "valor"     , $this->stMascaraLote );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTConfiguracao->setDado( "parametro" , "mascara_inscricao" );
    $this->obTConfiguracao->setDado( "valor"     , $this->stMascaraIM );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTConfiguracao->setDado( "parametro" , "navegacao_automatica" );
    $this->obTConfiguracao->setDado( "valor"     , $this->stNavegacaoAutomatico );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    if ( $obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

        return $obErro;
    }

    $this->obTConfiguracao->setDado( "parametro" , "ordem_entrega" );
    $this->obTConfiguracao->setDado( "valor"     , $this->montaOrdemEntrega() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    $this->obTConfiguracao->setDado( "parametro" , "valor_md" );
    $this->obTConfiguracao->setDado( "valor"     , $this->montaValorMD() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    $this->obTConfiguracao->setDado( "parametro" , "aliquotas" );
    $this->obTConfiguracao->setDado( "valor"     , $this->montaAliquota() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    $this->obTConfiguracao->setDado( "parametro" , "atrib_imovel" );
    $this->obTConfiguracao->setDado( "valor"     , $this->montaAtbImovel() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    $this->obTConfiguracao->setDado( "parametro" , "atrib_lote_urbano" );
    $this->obTConfiguracao->setDado( "valor"     , $this->montaAtbLoteUrbano() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    $this->obTConfiguracao->setDado( "parametro" , "atrib_lote_rural" );
    $this->obTConfiguracao->setDado( "valor"     , $this->montaAtbLoteRural() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    $this->obTConfiguracao->setDado( "parametro" , "atrib_edificacao" );
    $this->obTConfiguracao->setDado( "valor"     , $this->montaAtbEdificacao() );
    $this->verificaParametro( $boExiste, $boTransacao );
    if ($boExiste) {
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    } else {
        $obErro = $this->obTConfiguracao->inclusao( $boTransacao );
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro , $this->obTConfiguracao );

    return $obErro;
}

function montaAtbImovel()
{
    $stValores = "";
    if ($this->arAtbImovel) {
        foreach ($this->arAtbImovel as $stValor) {
            $stValores .= $stValor.",";
        }
    }

    $stValores = substr( $stValores , 0, strlen($stValores) - 1 );

    return $stValores;
}

function montaAtbLoteUrbano()
{
    $stValores = "";
    if ($this->arAtbLoteUrbano) {
        foreach ($this->arAtbLoteUrbano as $stValor) {
            $stValores .= $stValor.",";
        }
    }

    $stValores = substr( $stValores , 0, strlen($stValores) - 1 );

    return $stValores;
}

function montaAtbEdificacao()
{
    $stValores = "";
    if ($this->arAtbEdificacao) {
        foreach ($this->arAtbEdificacao as $stValor) {
            $stValores .= $stValor.",";
        }
    }

    $stValores = substr( $stValores , 0, strlen($stValores) - 1 );

    return $stValores;
}

function montaAtbLoteRural()
{
    $stValores = "";
    if ($this->arAtbLoteRural) {
        foreach ($this->arAtbLoteRural as $stValor) {
            $stValores .= $stValor.",";
        }
    }

    $stValores = substr( $stValores , 0, strlen($stValores) - 1 );

    return $stValores;
}

/**
    * Monta o array com a orderm de entrega
    * @access Public
    * @param  String $stOrdem
*/

function addAtbEdificacao($stValor)
{
    $this->arAtbEdificacao[] = trim( $stValor );
}

function addAtbImovel($stValor)
{
    $this->arAtbImovel[] = trim( $stValor );
}

function addAtbLotRural($stValor)
{
    $this->arAtbLoteRural[] = trim( $stValor );
}

function addAtbLotUrbano($stValor)
{
    $this->arAtbLoteUrbano[] = trim( $stValor );
}

function addOrdemEntrega($stOrdem) {//$stOrdem -> ex.:  "1 - Endereço Proprietário"
    $arOrdemEntrega = explode ( "-", $stOrdem );
    $this->arOrdemEntrega[] = '{'.trim( $arOrdemEntrega[0] ).', '.trim( $arOrdemEntrega[1] ).'}';
}

function addAliquota($stAliquota)
{
    $arTMP = explode ( "-", $stAliquota );
    $this->arOrdemAliquota[] = trim( $arTMP[0] );
    $this->arOrdemAliquota[] = trim( $arTMP[1] );
}

function montaAliquota()
{
    $stOrdemEntrega = "";
    if ($this->arOrdemAliquota) {
        foreach ($this->arOrdemAliquota as $stOrdEntrega) {
            $stOrdemEntrega .= $stOrdEntrega.",";
        }
    }

    $stOrdemEntrega = substr( $stOrdemEntrega , 0, strlen($stOrdemEntrega) - 1 );

    return $stOrdemEntrega;
}

function montaRSAliquota($stOrdemEntrega)
{
    if ($stOrdemEntrega) {
        $arOrdemEntrega = preg_split( "/,/" , $stOrdemEntrega );
        $mtOrdemEntrega = array();
        for ( $inCont = 0; $inCont < count( $arOrdemEntrega ); $inCont = $inCont + 2 ) {
            $mtOrdemEntrega[] = array( "numero" => trim($arOrdemEntrega[$inCont]), "nome" => trim($arOrdemEntrega[$inCont + 1]) );
        }

        $this->rsRSAliquota->preenche($mtOrdemEntrega);
    }
}

function addValorMD($stMD)
{
    $arMD = explode ( "-", $stMD );
    $this->arOrdemMD[] = trim( $arMD[0] );
    $this->arOrdemMD[] = trim( $arMD[1] );
}

function montaValorMD()
{
    $stOrdemEntrega = "";
    if ($this->arOrdemMD) {
        foreach ($this->arOrdemMD as $stOrdEntrega) {
            $stOrdemEntrega .= $stOrdEntrega.",";
        }
    }

    $stOrdemEntrega = substr( $stOrdemEntrega , 0, strlen($stOrdemEntrega) - 1 );

    return $stOrdemEntrega;
}

function montaRSValorMD($stOrdemEntrega)
{
    if ($stOrdemEntrega) {
        $arOrdemEntrega = preg_split( "/,/" , $stOrdemEntrega );
        $mtOrdemEntrega = array();
        for ( $inCont = 0; $inCont < count( $arOrdemEntrega ); $inCont = $inCont + 2 ) {
            $mtOrdemEntrega[] = array( "numero" => trim($arOrdemEntrega[$inCont]), "nome" => trim($arOrdemEntrega[$inCont + 1]) );
        }

        $this->rsRSMD->preenche($mtOrdemEntrega);
    }
}

/**
    * Monta uma string com a ordem dem de entrega formatada para o banco
    * @access Public
    * @return String $stOrdemEntrega
*/
function montaOrdemEntrega()
{
    $stOrdemEntrega = "{";
    if ($this->arOrdemEntrega) {
        foreach ($this->arOrdemEntrega as $stOrdEntrega) {
            $stOrdemEntrega .= $stOrdEntrega.",";
        }
    }

    $stOrdemEntrega = substr( $stOrdemEntrega , 0, strlen($stOrdemEntrega) - 1 );

    return $stOrdemEntrega."}";
}

/**
    * Monta um RecordSet com a orderm de entrega
    * @access Public
    * @param  String $stOrdemEntrega
*/
function montaRSOrdemEntrega($stOrdemEntrega)
{
    $stOrdemEntrega = preg_replace( "/[{}\"]/" , "", $stOrdemEntrega );
    $arOrdemEntrega = preg_split( "/,/" , $stOrdemEntrega );
    for ( $inCont = 0; $inCont < count( $arOrdemEntrega ); $inCont = $inCont + 2 ) {
        $mtOrdemEntrega[] = array( "cod_ordem" => trim($arOrdemEntrega[$inCont]), "nom_ordem" => trim($arOrdemEntrega[$inCont + 1]) );
    }
    $this->rsRSOrdemEntrega->preenche($mtOrdemEntrega);
}

/**
    * Recupera as configurações referentes ao cadastro imobiliário
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarConfiguracao($boTransacao = "")
{
    $obErro = $this->buscaModulo( $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;
    
    $this->obTConfiguracao->setDado( "cod_modulo", $this->inCodigoModulo );
    $this->obTConfiguracao->setDado( "exercicio" , Sessao::getExercicio());
    
    $this->obTConfiguracao->setDado( "parametro" , "codigo_localizacao" );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->boCodigoLocal = $rsConfiguracao->getCampo( "valor" );
    
    $this->obTConfiguracao->setDado( "parametro" , "numero_inscricao" );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->inNumeroIM = $rsConfiguracao->getCampo( "valor" );

    $this->obTConfiguracao->setDado( "parametro" , "mascara_lote" );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->stMascaraLote = $rsConfiguracao->getCampo( "valor" );

    $this->obTConfiguracao->setDado( "parametro" , "atrib_edificacao" );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->arAtbEdificacao = explode( ",", $rsConfiguracao->getCampo( "valor" ) );

    $this->obTConfiguracao->setDado( "parametro" , "atrib_imovel" );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->arAtbImovel = explode( ",", $rsConfiguracao->getCampo( "valor" ) );

    $this->obTConfiguracao->setDado( "parametro" , "atrib_lote_rural" );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->arAtbLoteRural = explode( ",", $rsConfiguracao->getCampo( "valor" ) );

    $this->obTConfiguracao->setDado( "parametro" , "atrib_lote_urbano" );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->arAtbLoteUrbano = explode( ",", $rsConfiguracao->getCampo( "valor" ) );

    $this->obTConfiguracao->setDado( "parametro" , "mascara_inscricao" );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->stMascaraIM = $rsConfiguracao->getCampo( "valor" );

    $this->obTConfiguracao->setDado( "parametro" , "navegacao_automatica" );
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->stNavegacaoAutomatico = $rsConfiguracao->getCampo( "valor" );

    $this->obTConfiguracao->setDado( "parametro" , "ordem_entrega");
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->montaRSOrdemEntrega( $rsConfiguracao->getCampo( "valor" ) );

    $this->obTConfiguracao->setDado( "parametro" , "valor_md");
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->montaRSValorMD( $rsConfiguracao->getCampo( "valor" ) );

    $this->obTConfiguracao->setDado( "parametro" , "aliquotas");
    $obErro = $this->obTConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );
    if( $obErro->ocorreu() )
        return $obErro;

    $this->montaRSAliquota( $rsConfiguracao->getCampo( "valor" ) );

    return $obErro;
}

/**
    * Recupera o codigo do modulo cadastro imobiliario
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function buscaModulo($boTransacao = "")
{
    if ( Sessao::read('acao') == "" ) {
        Sessao::write('acao', Sessao::read('acaoLote') );
    }

    $stFiltro  = " AND A.cod_acao = ".(Sessao::read('acao')!=""?Sessao::read('acao'):Sessao::read('acaoLote'))." ";
    $obErro = $this->obTAcao->recuperaRelacionamento( $rsRelacionamento, $stFiltro, "", $boTransacao );
    if ( !$this->getCodigoModulo() ) {
        if ( !$obErro->ocorreu() ) {
                $this->inCodigoModulo =  $rsRelacionamento->getCampo("cod_modulo");
        }
    }

    return $obErro;
}

/**
    * Recupera o codigo do Municipio e UF
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listaDadosMunicipio(&$arConfiguracao, $boTransacao = "")
{
    $rsRecorSet = new RecordSet;
    $stFiltro = " where cod_modulo = 2 and parametro = 'cod_municipio' or parametro = 'cod_uf' ";
    $stOrdem  = " exercicio desc limit 2  offset 0 ";
    $obErro = $this->obTConfiguracao->recuperaTodos( $rsCofiguracao, $stFiltro , $stOrdem, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $arConfiguracao = array();
        while ( !$rsCofiguracao->eof() ) {
            $arConfiguracao[$rsCofiguracao->getCampo("parametro")] = $rsCofiguracao->getCampo("valor");
            $rsCofiguracao->proximo();
        }
    }

    return $obErro;
}

/**
    * Recupera a Mascara de processo
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarMascaraProcesso(&$stMascaraProcesso , $boTransacao = "")
{
    $stFiltro  = " WHERE COD_MODULO = 5 AND parametro = 'mascara_processo' ";
    $stFiltro .= " AND  exercicio = '".$this->getAnoExercicio()."' ";
    $stOrdem   = " ORDER BY EXERCICIO DESC ";
    $obErro = $this->obTConfiguracao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
//    $this->obTConfiguracao->debug();
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $stMascaraProcesso = $rsRecordSet->getCampo( "valor" );
    }

    return $obErro;
}

/**
    * Recupera a Mascara de processo
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarMascaraLote(&$stMascaraProcesso , $boTransacao = "")
{
    if ( $this->getCodigoModulo() ) {
        $stFiltro = " WHERE COD_MODULO = ".$this->getCodigoModulo()." AND parametro = 'mascara_lote' ";
    }
    $stOrdem  = " ORDER BY EXERCICIO DESC ";
    $obErro = $this->obTConfiguracao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $stMascaraProcesso = $rsRecordSet->getCampo( "valor" );
    }

    return $obErro;
}

}

?>