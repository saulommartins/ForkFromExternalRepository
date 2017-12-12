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
* Classe de negócio OrganogramaLocal
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27860 $
$Name$
$Author: luiz $
$Date: 2008-01-31 15:32:42 -0200 (Qui, 31 Jan 2008) $

Casos de uso: uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php"    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php"       );

//INCLUD DAS CLASSES PARA  O TRATAMNTO DOS ATRIBUTOS
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                    );

/**
    * Classe de regra de negÃ³cio para Trecho
    * Data de CriaÃ§Ã£o: 07/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra
*/
class ROrganogramaLocal extends RCIMLogradouro
{
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var Integer
*/
var $inCodLocal;
/**
    * @access Private
    * @var Integer
*/
var $inCodLogradouro;
/**
    * @access Private
    * @var Integer
*/
var $inNumero;
/**
    * @access Private
    * @var Integer
*/
var $inFone;
/**
    * @access Private
    * @var Integer
*/
var $inRamal;
/**
    * @access Private
    * @var Boolean
*/
var $boDificilAcesso;
/**
    * @access Private
    * @var Boolean
*/
var $boInsalubre;
/**
    * @access Private
    * @var Object
*/
var $obTOrganogramaLocal;

/**
    * @access Public
    * @param Integer $valor
*/
function setDescricao($valor) { $this->stDescricao                   = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodLocal($valor) { $this->inCodLocal                    = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodLogradouro($valor) { $this->inCodLogradouro               = $valor; }

/**
    * @access Public
    * @param Integer $valo
*/
function setNumero($valor) { $this->inNumero                      = $valor; }
/**
    * Access Public
    * @param Integer $valor
*/
function setFone($valor) { $this->inFone                        = $valor; }

/**
    * Access Public
    * @param Integer $valor
*/
function setRamal($valor) { $this->inRamal                       = $valor; }

/**
    * Access Public
    * @param Integer $valor
*/
function setDificilAcesso($valor) { $this->boDificilAcesso               = $valor; }

/**
    * Access Public
    * @param Integer $valor
*/
function setInsalubre($valor) { $this->boInsalubre                   = $valor; }

/**
    * Access Public
    * @param Object $valor
*/
function setTOrganogramaLocal($valor) { $this->obTOrganogramaLocal           = $valor; }

/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;        }

/**
    * @access Public
    * @return integer
*/
function getCodLocal() { return $this->inCodLocal;          }
/**
    * @access Public
    * @return integer
*/
function getCodLogradouro() { return $this->inCodLogradouro;     }
/**
    * @access Public
    * @return Integer
*/
function getNumero() { return $this->inNumero;            }
/**
    * @access Public
    * @return Integer
*/
function getFone() { return $this->inFone;              }
/**
    * @access Public
    * @return Integer
*/
function getRamal() { return $this->inRamal;             }
/**
    * @access Public
    * @return Boolean
*/
function getDificilAcesso() { return $this->boDificilAcesso;     }
/**
    * @access Public
    * @return Boolean
*/
function getInsalubre() { return $this->boInsalubre;         }
/**
    * @access Public
    * @return Object
*/
function getobTOrganogramaLocal() { return $this->obTOrganogramaLocal; }

/**
     * Método construtor
     * @access Private
*/

function ROrganogramaLocal()
{
    parent::RCIMLogradouro();
    $this->setTOrganogramaLocal    ( new TOrganogramaLocal    );
    $this->obRCadastroDinamico = new RCadastroDinamico;
    $this->obRCadastroDinamico->setCodCadastro( 7 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo( 12 );
}

/**
    * Inclui os dados setados na tabela de Trecho
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
function incluirLocal($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
            $obErro = $this->obTOrganogramaLocal->proximoCod( $inCodLocal, $boTransacao );
            $this->setCodLocal( $inCodLocal );
            if ( !$obErro->ocorreu() ) {
                $this->obTOrganogramaLocal->setDado( "cod_local"     , $this->getCodLocal()     );
                $this->obTOrganogramaLocal->setDado( "descricao"     , $this->getDescricao()    );
                $this->obTOrganogramaLocal->setDado( "cod_logradouro", $this->getCodLogradouro());
                $this->obTOrganogramaLocal->setDado( "numero"        , $this->getNumero()       );
                $this->obTOrganogramaLocal->setDado( "fone"          , $this->getFone()         );
                $this->obTOrganogramaLocal->setDado( "ramal"         , $this->getRamal()        );
                $this->obTOrganogramaLocal->setDado( "dificil_acesso", $this->getDificilAcesso());
                $this->obTOrganogramaLocal->setDado( "insalubre"     , $this->getInsalubre()    );
                $obErro = $this->obTOrganogramaLocal->inclusao( $boTransacao );
            }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTOrganogramaLocal );

    return $obErro;
 }

 /**
     * Altera os dados do Trecho setado
     * @access Public
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
 function alterarLocal($boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
         $this->obTOrganogramaLocal->setDado( "cod_local"     , $this->getCodLocal()      );
         $this->obTOrganogramaLocal->setDado( "descricao"     , $this->getDescricao()     );
         $this->obTOrganogramaLocal->setDado( "cod_logradouro", $this->getCodLogradouro() );
         if ($this->getNumero() =='' ) {
         $this->obTOrganogramaLocal->setDado( "numero"        , 'null'                );
     } else {
        $this->obTOrganogramaLocal->setDado( "numero"        , $this->getNumero()        );
     };

     if ($this->getRamal() =='') {
        $this->obTOrganogramaLocal->setDado( "ramal"         , 'null'         );
     } else {
        $this->obTOrganogramaLocal->setDado( "ramal"         , $this->getRamal()     );
     };

     $this->obTOrganogramaLocal->setDado( "fone"          , $this->getFone()          );

     $this->obTOrganogramaLocal->setDado( "dificil_acesso", $this->getDificilAcesso() );
         $this->obTOrganogramaLocal->setDado( "insalubre"     , $this->getInsalubre()     );
         $obErro = $this->obTOrganogramaLocal->alteracao( $boTransacao );
     }
     $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTOrganogramaLocal );

     return $obErro;
 }

   # Validação das tabelas possíveis de exclusão que utilizam cod_local.
    public function validarExclusao()
    {
        $arTable = array();

        # Tabelas que referenciam a coluna cod_local, caso algum registro seja
        # encontrado em pelo menos uma, não permite a exclusão do local.
        $arTable[] = 'administracao.impressora';
        $arTable[] = 'empenho.despesas_fixas';
        $arTable[] = 'estagio'.Sessao::getEntidade().'.estagiario_estagio_local';
        $arTable[] = 'folhapagamento'.Sessao::getEntidade().'.configuracao_empenho_lla_local';
        $arTable[] = 'folhapagamento'.Sessao::getEntidade().'.configuracao_empenho_local';
        $arTable[] = 'frota.terceiros_historico';
        $arTable[] = 'ima'.Sessao::getEntidade().'.configuracao_banpara_local';
        $arTable[] = 'ima'.Sessao::getEntidade().'.configuracao_banrisul_local';
        $arTable[] = 'ima'.Sessao::getEntidade().'.configuracao_bb_local';
        $arTable[] = 'ima'.Sessao::getEntidade().'.configuracao_besc_local';
        $arTable[] = 'ima'.Sessao::getEntidade().'.configuracao_hsbc_local';
        $arTable[] = 'organograma.de_para_local';
        $arTable[] = 'patrimonio.inventario_historico_bem';
        $arTable[] = 'patrimonio.historico_bem';
        $arTable[] = 'patrimonio.arquivo_coletora_dados';
        $arTable[] = 'pessoal'.Sessao::getEntidade().'.adido_cedido_local';
        $arTable[] = 'pessoal'.Sessao::getEntidade().'.contrato_servidor_local';
        $arTable[] = 'pessoal'.Sessao::getEntidade().'.contrato_servidor_local_historico';
        $arTable[] = 'pessoal'.Sessao::getEntidade().'.lote_ferias_local';
        $arTable[] = 'tcepe.fonte_recurso_local';
        ###

        $boPermiteExclusao = true;
        $boFlagTransacao   = false;

        $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        $stSql = "";

        foreach ($arTable as $key => $table) {
            $stSql  = "  SELECT COUNT(cod_local) AS total";
            $stSql .= "    FROM ".$table;
            $stSql .= "   WHERE cod_local = ".$this->getCodLocal();
            
            $this->obTOrganogramaLocal->executaRecuperaSql($stSql, $rsValidaOrgao);

            if ($rsValidaOrgao->getCampo('total') > 0) {
                $boPermiteExclusao = false;
                break;
            }
        }

        # Deve ser retirado quando o script SQL excluir essa tabela do sistema.
        # Valida temporariamente a tabela do De <-> Para.
        $stSql  = "  SELECT COUNT(cod_local_organograma) AS total";
        $stSql .= "    FROM organograma.de_para_local";
        $stSql .= "   WHERE cod_local_organograma = ".$this->getCodLocal();

        $this->obTOrganogramaLocal->executaRecuperaSql($stSql, $rsValidaOrgao);

        if ($rsValidaOrgao->getCampo('total') > 0) {
            $boPermiteExclusao = false;
        }
        ###

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );

        return $boPermiteExclusao;

    }

 /**
     * Exclui o Trecho setado
     * @access Public
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
 function excluirLocal($boTransacao = "")
 {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ($this->validarExclusao()) {
        if ( !$obErro->ocorreu() ) {
                $this->obTOrganogramaLocal->setDado( "cod_local" , $this->getCodLocal() );
                $obErro = $this->obTOrganogramaLocal->exclusao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTOrganogramaLocal );
    } else {
        $obErro->setDescricao('Este Local não pode ser excluído porque esta sendo utilizado pelo sistema.');
    }

    return $obErro;
 }

 /**
     * Lista o relacionamento dos Logradouros com os trechos conforme o filtro setado
     * @access Public
     * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
 function listarLogradouros(&$rsRecordSet, $boTransacao = "")
 {
    $stFiltro = "";
     if ( $this->getCodLogradouro() ) {
         $stFiltro .= " AND L.cod_logradouro = ".$this->getCodLogradouro()." ";
     }
     if ($this->stNomeLogradouro) {
         $stFiltro .= " AND UPPER( NL.nom_logradouro ) ";
         $stFiltro .= "LIKE UPPER('".$this->stNomeLogradouro."%') ";
     }
     if ($this->inCodigoUF) {
         $stFiltro .= "  AND L.cod_uf = ".$this->inCodigoUF." ";
     }
     if ($this->inCodigoMunicipio) {
         $stFiltro .= "  AND L.cod_municipio = ".$this->inCodigoMunicipio." ";
     }
     $stOrder = " ORDER BY NL.nom_logradouro ";
     $obErro = $this->obTOrganogramaLocal->recuperaRelacionamentoLogradouro( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

     return $obErro;
 }

 /**
     * Lista os Trechos conforme o filtro setado
     * @access Public
     * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
     * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
     * @return Object Objeto Erro
 */
 function listarLocal(&$rsRecordSet, $boTransacao = "")
 {
     $stFiltro = "";
     if ($this->inCodLocal  !="") {
         $stFiltro .= " Where cod_local = ".$this->inCodLocal." ";
     }
     if ($this->stDescricao) {
         $stFiltro .= " WHERE UPPER( descricao ) ";
         $stFiltro .= "LIKE UPPER('%".$this->stDescricao."%') ";
     }
     $stOrder = " ORDER BY UPPER( descricao ) ASC";
     $obErro = $this->obTOrganogramaLocal->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
     if (!$obErro->ocorreu()) {
         $this->setCodLocal                 ( $rsRecordSet->getCampo("cod_local")           );
         $this->setCodLogradouro            ( $rsRecordSet->getCampo("cod_logradouro")      );
         $this->setNumero                   ( $rsRecordSet->getCampo("numero")              );
         $this->setFone                     ( $rsRecordSet->getCampo("fone")                );
         $this->setRamal                    ( $rsRecordSet->getCampo("ramal")               );
         $this->setDificilAcesso            ( $rsRecordSet->getCampo("dificil_acesso")      );
         $this->setInsalubre                ( $rsRecordSet->getCampo("insalubre")           );
         $this->setDescricao                ( $rsRecordSet->getCampo("descricao")           );
     }

     return $obErro;
 }
/**
    * Recupera os dados referentes a descricao conforme setado no filtro
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao ParÃ¢metro TransaÃ§Ã£o
    * @return Object Objeto Erro
*/
 function listarUnicoLocal(&$rsRecordSet , $boTransacao = "")
 {
    $stFiltro = "";

    if ($_REQUEST['stDescricao']) {
        $stFiltro .= " UPPER(descricao) LIKE UPPER('%".$_REQUEST['stDescricao']."%') AND ";
    }

    if ( $this->getCodLocal() !="") {
        $stFiltro .= " cod_local = ".$this->getCodLocal()." AND ";
    }
    $stOrdem = " ORDER BY descricao";
    $stFiltro = ($stFiltro) ?" WHERE ".substr($stFiltro,0,strlen($stFiltro)-4) : "";
    $obErro = $this->obTOrganogramaLocal->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;

 }
}
?>
