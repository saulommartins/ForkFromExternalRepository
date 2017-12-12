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
    * Classe de regra de negócio para CNAE
    * Data de Criação: 23/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMCnae.class.php 66548 2016-09-21 13:05:07Z evandro $

    * Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.5  2007/05/17 21:11:47  cercato
Bug #9273#

Revision 1.4  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelCnae.class.php"               );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCnaeFiscal.class.php"         );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMNivelCnaeValor.class.php"     );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeCnaeFiscal.class.php");

class RCEMCnae extends RCEMNivelCnae
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoCnae;
/**
    * @access Private
    * @var String
*/
var $stNomeCnae;
/**
    * @access Private
    * @var Object
*/
var $obTCEMCnaeFiscalNivel;
/**
    * @access Private
    * @var Object
*/
var $obTCEMCnaeFiscal;
/**
    * @access Private
    * @var Object
*/
var $roRCEMAtividade;
/**
    * @access Private
    * @var String
*/
var $stValor;//tabela SERVICO_NIVEL
/**
    * @access Private
    * @var String
*/
var $stValorCompostoCnae;//valor de todos os niveis de servicos concateneados
/**
    * @access Private
    * @var String
*/
var $stValorReduzidoCnae;//valor de todos os niveis que possuem servicos

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoCnae($valor) { $this->inCodigoCnae = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeCnae($valor) { $this->stNomeCnae   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function getValor() { return $this->stValor; }
/**
    * @access Public
    * @param String $valor
*/
function getValorCompostoCnae() { return $this->stValorCompostoCnaer; }
/**
    * @access Public
    * @param String $valor
*/
function getValorReduzidoCnae() { return $this->stValorReduzidoCnae; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoCnae() { return $this->inCodigoCnae; }
/**
    * @access Public
    * @return String
*/
function getNomeCnae() { return $this->stNomeCnae;   }
/**
    * @access Public
    * @param String $valor
*/
function setValor($valor) { $this->stValor = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorCompostoCnae($valor) { $this->stValorCompostoCnae = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setValorReduzidoCnae($valor) { $this->stValorReduzidoCnae = $valor; }
/**
     * Método construtor
     * @access Private
*/
function RCEMCnae(&$obRCEMAtividade)
{
    parent::RCEMNivelCnae();
    $this->obTCEMNivelCnaeValor      = new TCEMNivelCnaeValor;
    $this->obTCEMAtividadeCnaeFiscal = new TCEMAtividadeCnaeFiscal;
    $this->obTCEMCnaeFiscal          = new TCEMCnaeFiscal;
    $this->roRCEMAtividade           = &$obRCEMAtividade;
    $this->arChaveCnae               = array();
}

/**
    * Lista os Cnaes segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCnae(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " COD_VIGENCIA = ".$this->inCodigoVigencia." AND";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " COD_NIVEL = ".$this->inCodigoNivel." AND";
    }
    if ($this->inCodigoCnae) {
        $stFiltro .= " COD_CNAE = ".$this->inCodigoCnae." AND";
    }
    if ($this->stNomeCnae) {
        $stFiltro .= " UPPER(NOM_ATIVIDADE) LIKE UPPER('%".$this->stNomeCnae."%') AND";
    }
    if ($this->stNomeNivel) {
        $stFiltro .= " UPPER(NOM_NIVEL) LIKE UPPER('%".$this->stNomeNivel."%') AND";
    }
    if ( $this->stValorReduzidoCnae && ($this->stNomeNivel == 1 || $this->inCodigoNivel >= 3 ) ) {
        $stFiltro .= " valor_composto like '".$this->stValorReduzidoCnae."%' AND";
    } elseif ($this->stValorReduzidoCnae) {
        $stFiltro .= " valor_composto like '".$this->stValorReduzidoCnae.".%' AND";
    }
    if ($this->stValorCompostoCnae) {
        $stFiltro .= " valor_composto like '".$this->stValorCompostoCnae."%' AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrdem = " ORDER BY valor_composto";
    $obErro = $this->obTCEMCnaeFiscal->recuperaCnaeAtivo( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
//$this->obTCEMCnaeFiscal->debug();
    return $obErro;
}

/**
    * Lista os Cnaes com as Atividades segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCnaeAtividade(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ($this->inCodigoCnae) {
        $stFiltro .= " AND CF.COD_CNAE = ". $this->inCodigoCnae ." ";
    }

    if ($this->stNomeCnae) {
        $stFiltro .= " AND UPPER(CF.NOM_ATIVIDADE) LIKE UPPER('".$this->stNomeCnae."%')";
    }

    if ( $this->roRCEMAtividade->getCodigoAtividade() ) {
        $stFiltro .= " AND AT.COD_ATIVIDADE = ". $this->roRCEMAtividade->getCodigoAtividade()." ";
    }

    if ( $this->roRCEMAtividade->getNomeAtividade() ) {
        $stFiltro .= " AND UPPER(AT.NOM_ATIVIDADE) LIKE UPPER('".$this->roRCEMAtividade->getNomeAtividade()."%')";
    }

    $stOrdem = " ORDER BY CF.COD_CNAE ";
    $obErro = $this->obTCEMAtividadeCnaeFiscal->recuperaAtividadeCnae( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados do Cnae selecionada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarCnae($boTransacao = "")
{
    $obErro = new Erro;
    if ($this->inCodigoVigencia and $this->inCodigoNivel and $this->inCodigoCnae) {
        $obErro = $this->listarCnae( $rsCnae, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->stNomeCnae        = $rsCnae->getCampo( "nom_atividade" );
            $this->stNomeNivel       = $rsCnae->getCampo( "nom_nivel" );
            $this->stMascara         = $rsCnae->getCampo( "mascara" );
            $this->stValorCompostoCnae   = $rsCnae->getCampo( "valor_composto" );
            $this->stValorReduzidoCnae   = $rsCnae->getCampo( "valor_reduzido" );
            $arValor = explode( ".", $this->stValorReduzidoCnae );
            $this->stValor           = end( $arValor );
        }
    }

    return $obErro;
}

/**
    * Verifica se existem filhos do servico setadas, se houver retorna o erro informando
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaFilhosCnae($boTransacao = "")
{
    $inCodigoCnaeTmp = $this->inCodigoCnae;
    $inCodigoNivelTmp       = $this->inCodigoNivel;
    $this->inCodigoCnae = "";
    $this->inCodigoNivel       = "";
    $obErro = $this->listarCnae( $rsListaCnae, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsListaCnae->eof() ) {
        $obErro->setDescricao( "Existem CNAEs dependentes deste CNAE!" );
    }
    $this->inCodigoCnae = $inCodigoCnaeTmp;
    $this->inCodigoNivel   = $inCodigoNivelTmp;

    return $obErro;
}

/**
    * Valida o codigo do servico
    * @access Public
    * @param Integer $inCodigo Codigo do nivel superior
*/
function validaCodigoCnae($boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " AND LN.COD_VIGENCIA = ".$this->inCodigoVigencia." ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " AND LN.COD_NIVEL = ".$this->inCodigoNivel." ";
    }
    if ($this->inCodigoCnae) {
        $stFiltro .= " AND LN.COD_CNAE <> ".$this->inCodigoCnae." ";
    }
    if ($this->stValor) {
        $stFiltro .= " AND LPAD( LN.valor, length(NI.mascara),'0' ) =";
        $stFiltro .= " LPAD( '".$this->stValor."', length(NI.mascara), '0' ) ";
    }
    if ( count($this->arChaveCnae) ) {
        $stValorReduzidoCnae = $this->arChaveCnae[ count($this->arChaveCnae) - 1 ][3];
        $stFiltro .= " AND LN.valor_reduzido like '".$stValorReduzidoCnae."%' ";
    }
    $obErro = $this->obTCEMCnaeFiscal->recuperaCnaeAtivo( $rsRecordSet, $stFiltro, "" , $boTransacao );
    if ( !$rsRecordSet->eof() ) {
        $obErro->setDescricao( "Já existe um CNAE cadastrado com o código ".$this->stValor );
    }

    return $obErro;
}

/**
    * Adiciona no array  arChaveCnae os códigos dos CNAEs ao de niveis superiores
    * @access Public
    * @param Integer $inCodigo Codigo do nivel superior
*/
function addCodigoCnae($arChaveCnae)
{
    $this->arChaveCnae[] = $arChaveCnae;//[0] = cod_nivel | [1] = cod_cnae | [2] = valor
}

/**
    * Gera a mascara segundo o filtro setado
    * @access Public
    * @param  Object $stMascara String com a mascara
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function geraMascara(&$stMascara , $boTransacao = "")
{
    $obErro = $this->listarNiveis( $rsNivel, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stMascara = "";
        while ( !$rsNivel->eof() ) {
            $stMascara .= $rsNivel->getCampo( "mascara" ).".";
            $rsNivel->proximo();
        }
        if ($stMascara) {
            $stMascara = substr( $stMascara, 0, strlen( $stMascara ) - 1);
        }
    }

    return $obErro;
}

/**
    * Inclui dados setados na tabela de atividade_cnae
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function incluirAtividadeCnae($boFlagTransacao, $boTransacao = "")
{
    $boFlagTransacao = isset($boFlagTransacao) ? $boFlagTransacao: false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMAtividadeCnaeFiscal->setDado( "cod_atividade", $this->roRCEMAtividade->getCodigoAtividade() );
        $this->obTCEMAtividadeCnaeFiscal->setDado( "cod_cnae"     , $this->inCodigoCnae );
        $obErro = $this->obTCEMAtividadeCnaeFiscal->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeCnaeFiscal );

    return $obErro;
}

/**
    * Altera dados setados na tabela de atividade_cnae
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function alterarAtividadeCnae($boFlagTransacao, $boTransacao = "")
{
    $boFlagTransacao = isset($boFlagTransacao) ? $boFlagTransacao: false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMAtividadeCnaeFiscal->setDado( "cod_atividade", $this->roRCEMAtividade->getCodigoAtividade() );
        $this->obTCEMAtividadeCnaeFiscal->setDado( "cod_cnae"     , $this->inCodigoCnae );
        $obErro = $this->obTCEMAtividadeCnaeFiscal->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeCnaeFiscal );

    return $obErro;
}

/**
    * Altera dados setados na tabela de atividade_cnae
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function excluirAtividadeCnae($boFlagTransacao, $boTransacao = "")
{    
    $boFlagTransacao = isset($boFlagTransacao) ? $boFlagTransacao: false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMAtividadeCnaeFiscal->setDado( "cod_atividade", $this->roRCEMAtividade->getCodigoAtividade() );
        $this->obTCEMAtividadeCnaeFiscal->setDado( "cod_cnae"     , $this->inCodigoCnae );
        $obErro = $this->obTCEMAtividadeCnaeFiscal->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeCnaeFiscal );

    return $obErro;
}

}
?>
