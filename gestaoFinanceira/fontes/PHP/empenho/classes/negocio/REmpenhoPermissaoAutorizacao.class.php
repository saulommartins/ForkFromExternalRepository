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
    * Classe de Regra de Permissao para Autorizacao
    * Data de Criação   : 02/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: jose.eduardo $
    $Date: 2006-07-06 14:52:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-02.03.01, uc-02.03.02
*/

/*
$Log$
Revision 1.9  2006/07/06 17:50:00  jose.eduardo
Bug #6457#

Revision 1.8  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

 include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
 include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                       );
 include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php"                                );
 include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"                    );
 include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                                );

 /**
     * Classe de Regra de Permissao para Autorizacao
     * @author Analista: Jorge B. Ribarr
     * @author Desenvolvedor: Gelson W. Gonçalves
 */
 class REmpenhoPermissaoAutorizacao
 {
 /**
     * @access Private
     * @var String
 */
 var $stExercicio;
 /**
     * @access Private
     * @var Object
 */
 var $obROrcamentoUnidade;
 /**
     * @access Private
     * @var Object
 */
 var $obRUsuario;
 /**
     * @access Private
     * @var Object
 */
 var $obROrcamentoDespesa;

 /**
     * @access Private
     * @var Array
 */
 var $arPermissoes;

 /**
     * @access Public
     * @param String $Valor
 */
 function setExercicio($valor) { $this->stExercicio = $valor;                    }
 /**
      * @access Public
      * @param Object $valor
 */
 function setROrcamentoUnidade($valor) { $this->obROrcamentoUnidade = $valor;            }
 /**
      * @access Public
      * @param Object $valor
 */
 function setRUsuario($valor) { $this->obRUsuario = $valor;                     }
 /**
      * @access Public
      * @param Object $valor
 */
 function setROrcamentoDespesa($valor) { $this->obROrcamentoDespesa = $valor;            }
 /**
      * @access Public
      * @param Array $valor
 */
 function setPermissoes($valor) { $this->arPermissoes = $valor;                   }

 /**
     * @access Public
     * @return String
 */
 function getExercicio() { return $this->stExercicio;                              }
 /**
      * @access public
      * @return object
 */
 function getROrcamentoUnidade() { return $this->obROrcamentoUnidade;                      }
 /**
      * @access public
      * @return object
 */
 function getRUsuario() { return $this->obRUsuario;                               }
 /**
      * @access public
      * @return object
 */
 function getROrcamentoDespesa() { return $this->obROrcamentoDespesa;                      }
 /**
      * @access public
      * @return Array
 */
 function getPermissoes() { return $this->arPermissoes;                             }

/**
     * Método construtor
     * @access Public
*/
function REmpenhoPermissaoAutorizacao()
{
    $this->obTransacao                    = new Transacao                   ;
    $this->obROrcamentoUnidade            = new ROrcamentoUnidadeOrcamentaria        ;
    $this->obRUsuario                     = new RUsuario                    ;
    $this->obROrcamentoDespesa            = new ROrcamentoDespesa                    ;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPermissaoAutorizacao.class.php"       );
    $obTEmpenhoPermissaoAutorizacao = new TEmpenhoPermissaoAutorizacao;

    $obTEmpenhoPermissaoAutorizacao->setDado( "exercicio"  , $this->stExercicio );
    $obTEmpenhoPermissaoAutorizacao->setDado( "num_orgao"  , $this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
    $obTEmpenhoPermissaoAutorizacao->setDado( "num_unidade", $this->obROrcamentoUnidade->getNumeroUnidade() );
    $obTEmpenhoPermissaoAutorizacao->setDado( "numcgm"     , $this->obRUsuario->obRCGM->getNumCGM() );

    $obErro = $obTEmpenhoPermissaoAutorizacao->recuperaPorChave( $rsRecordSet, $boTransacao  );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obRUsuario->consultar( $rsUsuario , $boTransacao ) ;
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obROrcamentoUnidade->consultar( $rsUnidade, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                //$this->obROrcamentoUnidade->obRUnidade->setNomUnidade( $rsUnidade->getCampo( "nom_unidade" ) );
                $this->obROrcamentoUnidade->stNomUnidade = $rsUnidade->getCampo( "nom_unidade" ) ;
                //$this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->obROrgao->setNomOrgao( $rsUnidade->getCampo( "nom_orgao" ) );
                $this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->stNomeOrgao = $rsUnidade->getCampo( "nom_orgao" );
            }
        }
    }

    return $obErro;
}
/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPermissaoAutorizacao.class.php"       );
    $obTEmpenhoPermissaoAutorizacao = new TEmpenhoPermissaoAutorizacao;

    if($this->stExercicio)
        $stFiltro  = " exercicio = '" . $this->stExercicio . "'  AND ";
    if($this->obROrcamentoUnidade->getNumeroUnidade())
        $stFiltro .= " num_unidade = " .$this->obROrcamentoUnidade->getNumeroUnidade(). " AND ";
    if($this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao())
        $stFiltro .= " num_orgao = " . $this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() . " AND ";
    if($this->obRUsuario->obRCGM->getNumCGM())
        $stFiltro .= " numcgm = " . $this->obRUsuario->obRCGM->getNumCGM() . " AND ";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "exercicio, numcgm, num_orgao, num_unidade";
    $obErro = $obTEmpenhoPermissaoAutorizacao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Exclui Permissão Autorização
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPermissaoAutorizacao.class.php"       );
    $obTEmpenhoPermissaoAutorizacao = new TEmpenhoPermissaoAutorizacao;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obTEmpenhoPermissaoAutorizacao->setDado( "exercicio"  , $this->stExercicio );
        $obTEmpenhoPermissaoAutorizacao->setDado( "numcgm"     , $this->obRUsuario->obRCGM->getNumCGM());
        $obErro = $obTEmpenhoPermissaoAutorizacao->exclusao( $boTransacao );
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoPermissaoAutorizacao );

    return $obErro;
}
/**
    * Incluir Permissão Autorização
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPermissaoAutorizacao.class.php"       );
    $obTEmpenhoPermissaoAutorizacao = new TEmpenhoPermissaoAutorizacao;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->excluir( $boTransacao );
        if (!$obErro->ocorreu()) {
            if ( sizeof( $this->arPermissoes ) ) {
                foreach ($this->arPermissoes as $arTEMP) {

                    $obTEmpenhoPermissaoAutorizacao->setDado( "exercicio"  , $this->stExercicio );
                    $obTEmpenhoPermissaoAutorizacao->setDado( "numcgm"     , $this->obRUsuario->obRCGM->getNumCGM());
                    $obTEmpenhoPermissaoAutorizacao->setDado( "num_orgao"  , $arTEMP['num_orgao']  );
                    $obTEmpenhoPermissaoAutorizacao->setDado( "num_unidade", $arTEMP['num_unidade']);

                    $obErro = $obTEmpenhoPermissaoAutorizacao->inclusao( $boTransacao );
                    if ($obErro->ocorreu()) {
                        break;
                    }
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTEmpenhoPermissaoAutorizacao );

    return $obErro;
}
/**
    * Lista Orgãos da Despesa cfe Entidade que o Usuário pode acessar.
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarOrgaoDespesaEntidadeUsuario(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    $boTransacao = false;
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPermissaoAutorizacao.class.php"       );
    $obTEmpenhoPermissaoAutorizacao = new TEmpenhoPermissaoAutorizacao;

    if($this->stExercicio)
        $stFiltro  = " ue.exercicio = '" . $this->stExercicio . "' AND ";
    if($this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade())
        $stFiltro .= " ue.cod_entidade = " . $this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade() . " AND ";
    if($this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao())
        $stFiltro .= " oo.num_orgao = " . $this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() . " AND ";
    if($this->obRUsuario->obRCGM->getNumCGM())
        $stFiltro .= " pa.numcgm = " . $this->obRUsuario->obRCGM->getNumCGM() . " AND ";
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : " oo.num_orgao ";
    $obErro = $obTEmpenhoPermissaoAutorizacao->recuperaOrgaoDespesaEntidadeUsuario( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Lista Unidades de Despesa cfe Entidade que o Usuário pode acessar.
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUnidadeDespesaEntidadeUsuario(&$rsRecordSet, $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoPermissaoAutorizacao.class.php"       );
    $obTEmpenhoPermissaoAutorizacao = new TEmpenhoPermissaoAutorizacao;

    if($this->stExercicio)
        $stFiltro  = " ue.exercicio = '" . $this->stExercicio . "'  AND ";
    if($this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade())
        $stFiltro .= " ue.cod_entidade = " . $this->obROrcamentoDespesa->obROrcamentoEntidade->getCodigoEntidade() . " AND ";
    if($this->obRUsuario->obRCGM->getNumCGM())
        $stFiltro .= " pa.numcgm = " . $this->obRUsuario->obRCGM->getNumCGM() . " AND ";
    if($this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao())
        $stFiltro .= " ou.num_orgao = " . $this->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() . " AND ";
    if($this->obROrcamentoUnidade->getNumeroUnidade())
        $stFiltro .= " ou.num_unidade = " . $this->obROrcamentoUnidade->getNumeroUnidade() . " AND ";
    $stFiltro = ($stFiltro) ? " AND " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "ou.num_orgao, ou.num_unidade";

    $obErro = $obTEmpenhoPermissaoAutorizacao->recuperaUnidadeDespesaEntidadeUsuario( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
