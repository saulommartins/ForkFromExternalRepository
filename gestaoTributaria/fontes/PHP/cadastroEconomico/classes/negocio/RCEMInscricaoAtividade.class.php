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
    * Classe de regra de negócio para Atividade
    * Data de Criação: 29/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMInscricaoAtividade.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.17  2007/03/20 14:43:35  cassiano
Bug #8771#

Revision 1.16  2007/03/19 15:38:09  cassiano
Bug #8771#

Revision 1.15  2007/03/02 14:50:13  dibueno
*** empty log message ***

Revision 1.14  2006/12/07 16:41:45  cercato
Bug #7770#

Revision 1.13  2006/11/20 10:10:57  cercato
bug #7438#

Revision 1.12  2006/11/20 09:53:11  cercato
bug #7438#

Revision 1.11  2006/11/17 12:43:15  domluc
Correção Bug #7437#

Revision 1.10  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php"          );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeCadastroEconomico.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMElementoAtividade.class.php"          );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php"                     );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"            );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoAtividadeCadEcon.class.php"   );

class RCEMInscricaoAtividade
{
/**
* @access Private
* @var Object
*/
var $obTCEMProcessoAtividadeCadEcon;

/**
    * @access Private
    * @var Boolean
*/
var $boPrincipal;
/**
    * @access Private
    * @var Date
*/
var $dtDataInicio;
/**
    * @access Private
    * @var Date
*/
var $dtDataTermino;
/**
* @access Private
* @var Object
*/
var $obTCEMAtividadeCadastroEconomico;
/**
* @access Private
* @var Object
*/
var $roRCEMInscricaoEconomica;
/**
    * @access Private
    * @var Object
*/
var $arRCEMAtividade;
/**
    * @access Private
    * @var Object
*/
var $roUltimaAtividade;
/**
     * @access Private
     * @var Integer
 */
var $inOcorrencia;

//SETTERS
/**
    * @access Public
    * @param Boolean $valor
*/
function setPrincipal($valor) { $this->boPrincipal = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataInicio($valor) { $this->dtDataInicio = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataTermino($valor) { $this->dtDataTermino = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setOcorrencia($valor) { $this->inOcorrencia = $valor; }

//GETTERS
/**
    * @access Public
    * @return Boolean
*/
function getPrincipal() { return $this->boPrincipal; }
/**
    * @access Public
    * @return Date
*/
function getDataInicio() { return $this->dtDataInicio; }
/**
    * @access Public
    * @return Date
*/
function getDataTermino() { return $this->dtDataTermino; }
/**
    * @access Public
    * @return Integer
*/
function getOcorrencia() { return $this->inOcorrencia; }

/**
     * Método construtor
     * @access Private
*/
function RCEMInscricaoAtividade(&$RCEMInscricaoEconomica)
{
    $this->obTCEMProcessoAtividadeCadEcon   = new TCEMProcessoAtividadeCadEcon;
    $this->obTCEMAtividadeCadastroEconomico = new TCEMAtividadeCadastroEconomico;
    $this->obTCEMElementoAtividade          = new TCEMElementoAtividade;
    $this->roRCEMInscricaoEconomica         = &$RCEMInscricaoEconomica;
    //$this->obRCEMAtividade                  = new RCEMAtividade;
    $this->obRCEMElemento                   = new RCEMElemento( new RCEMAtividade );
    $this->obTransacao                      = new Transacao;
    $this->obTCEMCadastroEconomico          = new TCEMCadastroEconomico;
    $this->arRCEMAtividade                  = array();
}

/**
    * Adiciona um objeto de atividade
    * @access Public
*/
function addAtividade()
{
    $this->arRCEMAtividade[] = new RCEMAtividade( $this );
    $this->roUltimaAtividade = &$this->arRCEMAtividade[ count($this->arRCEMAtividade) - 1 ];
}

/**
    * Recupera as atividades inscritas em uma inscricao economica
    * @access Public
    * @param  Object $arConfiguracao Retorna o array preenchido
    * @return Object Objeto Erro
*/
function listarAtividadesInscricao(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " ace.inscricao_economica = ".$this->roRCEMInscricaoEconomica->getInscricaoEconomica();

        $stFiltro .= " AND ace.ocorrencia_atividade = ( ";
        $stFiltro .= "     SELECT ";
        $stFiltro .= "         MAX(ocorrencia_atividade) AS ocorrencia_atividade";
        $stFiltro .= "     FROM ";
        $stFiltro .= "         economico.atividade_cadastro_economico ";
        $stFiltro .= "     WHERE ";
        $stFiltro .= "         inscricao_economica = ".$this->roRCEMInscricaoEconomica->getInscricaoEconomica();
        $stFiltro .= " ) AND ";
    }
    if ( $this->roUltimaAtividade->getCodigoAtividade() ) {
        $stFiltro .= " ace.cod_atividade = ".$this->roUltimaAtividade->getCodigoAtividade()." AND ";
    }
    if ( $this->roRCEMInscricaoEconomica->obRCGM->getNumCGM() ) {
        $stFiltro .= " cgm.numcgm = ".$this->roRCEMInscricaoEconomica->obRCGM->getNumCGM()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY inscricao_economica ";
    $obErro = $this->obTCEMAtividadeCadastroEconomico->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Recupera as atividades inscritas em uma inscricao economica
    * @access Public
    * @param  Object $arConfiguracao Retorna o array preenchido
    * @return Object Objeto Erro
*/
function consultarAtividadesInscricao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    $boMaxOcorrencia = true;
    if ( $this->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " AND ATE.INSCRICAO_ECONOMICA = ". $this->roRCEMInscricaoEconomica->getInscricaoEconomica();
    }

    if ( $this->obRCEMElemento->roCEMAtividade->getOcorrenciaAtividade() ) {
        $stFiltro .= " AND ATE.ocorrencia_atividade = ".$this->obRCEMElemento->roCEMAtividade->getOcorrenciaAtividade();
        $boMaxOcorrencia = false;
    }

    if( $this->roUltimaAtividade )
        if ( $this->roUltimaAtividade->getCodigoAtividade() ) {
            $stFiltro .= " AND ATV.COD_ATIVIDADE = ".$this->roUltimaAtividade->getCodigoAtividade();
        }

    $stOrder = " ORDER BY inscricao_economica ";
    $obErro = $this->obTCEMAtividadeCadastroEconomico->recuperaAtividadeInscricao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao, $boMaxOcorrencia );

    return $obErro;
}

/**
    * Adiciona uma atividade para inscricao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirAtividadeInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMAtividadeCadastroEconomico->setDado( "principal" , $this->getPrincipal() );
        $this->obTCEMAtividadeCadastroEconomico->setDado( "inscricao_economica", $this->roRCEMInscricaoEconomica->getInscricaoEconomica() );
        $this->obTCEMAtividadeCadastroEconomico->setDado( "cod_atividade" , $this->roUltimaAtividade->getCodigoAtividade() );
        $this->obTCEMAtividadeCadastroEconomico->setDado( "dt_inicio" , $this->getDataInicio() );
        $this->obTCEMAtividadeCadastroEconomico->setDado( "dt_termino" , $this->getDataTermino() );
        $this->obTCEMAtividadeCadastroEconomico->setDado( "ocorrencia_atividade" , $this->getOcorrencia() );
        $obErro = $this->obTCEMAtividadeCadastroEconomico->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeCadastroEconomico );

    return $obErro;
}

function gerarOcorrenciaAtividade(&$inOcorrencia, $inInscricaoEconomica, $boTransacao = "")
{
    $obTCEMAtividadeCadastroEconomico = new TCEMAtividadeCadastroEconomico();
    $obTCEMAtividadeCadastroEconomico->setCampoCod("inscricao_economica");
    $obTCEMAtividadeCadastroEconomico->setComplementoChave("");
    $obTCEMAtividadeCadastroEconomico->setDado( "inscricao_economica", $inInscricaoEconomica );
    $obErro = $obTCEMAtividadeCadastroEconomico->proximoCod( $inOcorrencia , $boTransacao );

    return $obErro;
}

/**
    * Alterar atividade para inscricao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarAtividadeInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inTmpCodigoAtividade = $this->roUltimaAtividade->getCodigoAtividade();
        $this->roUltimaAtividade->setCodigoAtividade("");
        $obErro = $this->consultarAtividadesInscricao( $rsRecordSet, $boTransacao );
        $this->roUltimaAtividade->setCodigoAtividade($inTmpCodigoAtividade);
        if ( !$obErro->ocorreu() ) {
            $arAtividade = array();
            while ( !$rsRecordSet->eof() ) {
                $inChaveAtividade = $rsRecordSet->getCampo( "cod_atividade" );
                $arAtividade[$inChaveAtividade] = true;
                $rsRecordSet->proximo();
            }
            foreach ($this->arRCEMAtividade as $obRCEMAtividade) {
                $inChave = $this->roUltimaAtividade->getCodigoAtividade();
                if ( !isset($arAtividade[$inChave]) ) {

                    $this->obTCEMAtividadeCadastroEconomico->setDado( "inscricao_economica", $this->roRCEMInscricaoEconomica->getInscricaoEconomica() );
                    $this->obTCEMAtividadeCadastroEconomico->setDado( "cod_atividade" , $obRCEMAtividade->getCodigoAtividade() );

                    $obErro = $this->obTCEMAtividadeCadastroEconomico->proximoCod( $inOcorrencia , $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obTCEMAtividadeCadastroEconomico->setDado( "ocorrencia_atividade" , $inOcorrencia );
                        $obErro = $this->obTCEMAtividadeCadastroEconomico->inclusao( $boTransacao );
                    }
                    $this->incluirAtividadeInscricao( $boTransacao );
                } else {
                    unset($arAtividade[$inChave]);
                }
            }
            if ( !$obErro->ocorreu() ) {
                foreach ($arAtividade as $inKey => $valor) {
                    $this->roUltimaAtividade->setCodigoAtividade( $inKey );
                    $obErro = $this->consultarAtividadesInscricao( $rsAtividade, $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        while ( !$rsAtividade->eof() ) {
                            if ( $this->getDataTermino() != '' ) {
                                $dataTermino = $this->getDataTermino();
                            } else {
                                $dataTermino = data('d/m/Y');
                            }
                            $this->obTCEMAtividadeCadastroEconomico->setDado( "cod_atividade", $inKey );
                            $this->obTCEMAtividadeCadastroEconomico->setDado( "inscricao_economica" , $this->roRCEMInscricaoEconomica->getInscricaoEconomica() );
                            $this->obTCEMAtividadeCadastroEconomico->setDado( "dt_termino"   , $dataTermino );
                            $this->obTCEMAtividadeCadastroEconomico->setDado( "principal"    , $this->getPrincipal() );
                            $this->obTCEMAtividadeCadastroEconomico->setDado( "ocorrencia_atividade" , $rsAtividade->getCampo( "ocorrencia_atividade" ) );
                            $obErro = $this->obTCEMAtividadeCadastroEconomico->exclusao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                            $rsAtividade->proximo();
                        }
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeCadastroEconomico );

    return $obErro;
}

/**
    * Exclui os dados referentes a Atividade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirAtividadeInscricao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMAtividadeCadastroEconomico->setCampoCod("");
        $this->obTCEMAtividadeCadastroEconomico->setDado( "inscricao_economica" , $this->roRCEMInscricaoEconomica->getInscricaoEconomica() );
        $this->obTCEMAtividadeCadastroEconomico->setDado( "cod_atividade" , $this->roUltimaAtividade->getCodigoAtividade() );

        $obErro = $this->obTCEMAtividadeCadastroEconomico->exclusao( $boTransacao );
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeCadastroEconomico );

    return $obErro;
}

/**
    * Lista os Elementos da Atividade segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoAtividade(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->roUltimaAtividade->getCodigoAtividade() ) {
        $stFiltro .= " AND AC.COD_ATIVIDADE = ".$this->roUltimaAtividade->getCodigoAtividade()." ";
    }

    if ($this->roUltimaAtividade->roUltimoElemento->inCodigoElemento) {
        $stFiltro .= " AND EL.COD_ELEMENTO = ".$this->roUltimaAtividade->roUltimoElemento->inCodigoElemento." ";
    }

    if ($this->roUltimaAtividade->roUltimoElemento->stNomeElemento) {
        $stFiltro .= " AND UPPER(EL.NOM_ELEMENTO) LIKE UPPER( '".$this->roUltimaAtividade->roUltimoElemento->stNomeElemento."%')";
    }

    if ( $this->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " AND AC.INSCRICAO_ECONOMICA = ". $this->roRCEMInscricaoEconomica->getInscricaoEconomica()." ";
    }

    $stOrdem = " ORDER BY EL.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoAtividade->recuperaElementoInscricao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os Elementos da Atividade segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoPorUltimaOcorrenciaAtividade(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->roUltimaAtividade->getCodigoAtividade() ) {
        $stFiltro .= " AND atividade_cadastro_economico.COD_ATIVIDADE = ".$this->roUltimaAtividade->getCodigoAtividade()." ";
    }

    if ($this->roUltimaAtividade->roUltimoElemento->inCodigoElemento) {
        $stFiltro .= " AND elemento.COD_ELEMENTO = ".$this->roUltimaAtividade->roUltimoElemento->inCodigoElemento." ";
    }

    if ($this->roUltimaAtividade->roUltimoElemento->stNomeElemento) {
        $stFiltro .= " AND UPPER(elemento.NOM_ELEMENTO) LIKE UPPER( '".$this->roUltimaAtividade->roUltimoElemento->stNomeElemento."%')";
    }

    $stOrdem = " ORDER BY EL.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoAtividade->recuperaElementoPorUltimaOcorrenciaAtividadeEconomica( $rsRecordSet,$this->roRCEMInscricaoEconomica->getInscricaoEconomica(), $stFiltro, $stOrdem, $boTransacao );
//    $this->obTCEMElementoAtividade->debug();
    return $obErro;
}

/**
    * Lista os Elementos da Atividade segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoUltimaOcorrenciaAtividade(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->roUltimaAtividade->getCodigoAtividade() ) {
        $stFiltro .= " AND AC.COD_ATIVIDADE = ".$this->roUltimaAtividade->getCodigoAtividade()." ";
    }

    if ($this->roUltimaAtividade->roUltimoElemento->inCodigoElemento) {
        $stFiltro .= " AND EL.COD_ELEMENTO = ".$this->roUltimaAtividade->roUltimoElemento->inCodigoElemento." ";
    }

    if ($this->roUltimaAtividade->roUltimoElemento->stNomeElemento) {
        $stFiltro .= " AND UPPER(EL.NOM_ELEMENTO) LIKE UPPER( '".$this->roUltimaAtividade->roUltimoElemento->stNomeElemento."%')";
    }

    if ( $this->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " AND AC.INSCRICAO_ECONOMICA = ". $this->roRCEMInscricaoEconomica->getInscricaoEconomica()." ";
    }

    $stOrdem = " ORDER BY EL.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoAtividade->recuperaElementoInscricao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Listar Elementos Inscricao Economica Excluindo Elementos Baixados
    * @access Public
    * @param Object $rsRecordSet
    * @param Object $obTransacao Parametro Transacao
    * @return Object Erro
*/
function listarElementoAtividadeInscricao(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->roUltimaAtividade->getCodigoAtividade() ) {
        $stFiltro .= " AND AC.COD_ATIVIDADE = ".$this->roUltimaAtividade->getCodigoAtividade()." ";
    }

    if ($this->roUltimaAtividade->roUltimoElemento->inCodigoElemento) {
        $stFiltro .= " AND EL.COD_ELEMENTO = ".$this->roUltimaAtividade->roUltimoElemento->inCodigoElemento." ";
    }

    if ($this->roUltimaAtividade->roUltimoElemento->stNomeElemento) {
        $stFiltro .= " AND UPPER(EL.NOM_ELEMENTO) LIKE UPPER( '".$this->roUltimaAtividade->roUltimoElemento->stNomeElemento."%')";
    }

    if ( $this->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " AND AC.INSCRICAO_ECONOMICA = ". $this->roRCEMInscricaoEconomica->getInscricaoEconomica()." ";
    }

    $stOrdem = " ORDER BY EL.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoAtividade->recuperaElementoInscricaoAtividade( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    //$this->obTCEMElementoAtividade->debug();
    return $obErro;
}

/**
    * Lista os Elementos da Atividade segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarElementoAtividadeEconomico(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ( $this->roUltimaAtividade->getCodigoAtividade() ) {
        $stFiltro .= " AND EA.COD_ATIVIDADE = ".$this->roUltimaAtividade->getCodigoAtividade()." ";
    }

    if ($this->roUltimaAtividade->roUltimoElemento->inCodigoElemento) {
        $stFiltro .= " AND EA.COD_ELEMENTO = ".$this->roUltimaAtividade->roUltimoElemento->inCodigoElemento." ";
    }

    if ($this->roUltimaAtividade->roUltimoElemento->stNomeElemento) {
        $stFiltro .= " AND UPPER(E.NOM_ELEMENTO) LIKE UPPER( '".$this->roUltimaAtividade->roUltimoElemento->stNomeElemento."%')";
    }

    if ( $this->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " AND EA.INSCRICAO_ECONOMICA = ". $this->roRCEMInscricaoEconomica->getInscricaoEconomica()." ";
    }

    $stOrdem = " ORDER BY EA.COD_ELEMENTO ";
    $obErro = $this->obTCEMElementoAtividade->recuperaElementoAtividadeEconomico( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}
/**
    * Lista os Elementos da Atividade segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMontaAtividadesInscricao(&$rsRecordSet, $boTransacao = "")
{
    $this->obTCEMAtividadeCadastroEconomico = new TCEMAtividadeCadastroEconomico;
    $stFiltro = "";

    if ( $this->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " b.inscricao_economica = ".$this->roRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    }

    if ( $this->obRCEMElemento->roCEMAtividade->getOcorrenciaAtividade() ) {
        $stFiltro .= " b.ocorrencia_atividade = ".$this->obRCEMElemento->roCEMAtividade->getOcorrenciaAtividade()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro)-4 );
    }

    $stOrdem = " ORDER BY c.cod_estrutural ";
    $obErro = $this->obTCEMAtividadeCadastroEconomico->recuperaAtividadesInscricao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    #$this->obTCEMAtividadeCadastroEconomico->debug();

    return $obErro;
}

function listarProcessosAtividadesCadastroEconomico(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " inscricao_economica = ".$this->roRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    }

    if ( $this->roRCEMInscricaoEconomica->getAnoExercicio() ) {
        $stFiltro .= " ano_exercicio = ".$this->roRCEMInscricaoEconomica->getAnoExercicio()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $this->obTCEMProcessoAtividadeCadEcon->recuperaListaProcessos( $rsRecordSet, $stFiltro, "", $boTransacao );
}

}
?>
