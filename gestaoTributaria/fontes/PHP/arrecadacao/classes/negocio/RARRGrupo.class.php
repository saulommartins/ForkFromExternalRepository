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
     * Classe de regra de negócio para arrecadacao grupo
     * Data de Criação: 12/05/2005

     * @author Analista: Fabio Bertoldi Rodrigues
     * @author Desenvolvedor: Lucas Teixeira Stephanou
     * @package GESTAO_TRIBUTARIA
     * @subpackage REGRA

    * $Id: RARRGrupo.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.03.02
 */

/*
$Log$
Revision 1.30  2007/03/07 21:52:43  rodrigo
Bug #8439#

Revision 1.29  2007/03/05 19:15:30  dibueno
*** empty log message ***

Revision 1.28  2007/03/01 14:49:12  rodrigo
Bug #8439#

Revision 1.27  2007/01/31 17:42:52  cercato
correcao do bug (dt_vencimento eh nulo).

Revision 1.26  2006/11/01 18:17:58  dibueno
Bug #7285

Revision 1.25  2006/11/01 12:23:46  dibueno
Retirada do "UPPER" da coluna exercicio

Revision 1.24  2006/10/30 13:15:31  dibueno
#7285#

Revision 1.23  2006/10/19 18:46:11  cercato
correcao para deletar dados de acordo com ano exercicio.

Revision 1.22  2006/10/06 10:59:58  cercato
correcao da inclusao/alteracao/exclusao de atributos dinamicos do grupo.

Revision 1.21  2006/09/15 11:50:14  fabio
corrigidas tags de caso de uso

Revision 1.20  2006/09/15 10:48:45  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

 include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
 include_once ( CAM_GT_ARR_MAPEAMENTO."TARRGrupoCredito.class.php"      );
 include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCreditoGrupo.class.php"      );
 include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPermissao.class.php"    );
 include_once ( CAM_GT_ARR_MAPEAMENTO."TARRAcrescimoGrupo.class.php"    );
 include_once ( CAM_GT_ARR_MAPEAMENTO."TARRArrecadacaoModulos.class.php");
 include_once ( CAM_GT_ARR_MAPEAMENTO."TARRRegraDesoneracaoGrupo.class.php");
 include_once ( CAM_GT_MON_NEGOCIO."RMONAcrescimo.class.php"              );
 include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php"                );
 include_once ( CAM_GT_ARR_NEGOCIO."RARRCalendarioFiscal.class.php"       );
 include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"          );
 include_once ( CAM_GT_ARR_MAPEAMENTO."TARRAtributoGrupoValor.class.php");
 include_once ( CAM_GT_ARR_MAPEAMENTO."TARRAtributoGrupo.class.php"     );
 /**
     * Classe de regra de negócio para arrecadacao grupo
     * Data de Criação: 12/05/2005

     * @author Analista: Fabio Bertoldi Rodrigues
     * @author Desenvolvedor: Lucas Teixeira Stephanou
     * @package GESTAO_TRIBUTARIA
     * @subpackage REGRA
 */

 class RARRGrupo
 {
 var $stFuncaoDesoneracao;

 /**
     * @access Private
     * @var Integer
 */
 var $inCodGrupo;
 /**
     * @access Private
     * @var Integer
 */
 var $inCodModulo;
 /**
     * @access Private
     * @var String
 */
 var $stDescricao;
 /**
     * @access Private
     * @var String
 */
 var $stExercicio;
 /**
     * @access Private
     * @var Array
 */
 var $arCreditos;
 /**
     * @access Private
     * @var Array
 */
 var $arAcrescimos;
 /**
     * @access Private
     * @var Array Object
 */
 var $arRMONCredito;
 /**
     * @access Private
     * @var Array Object
 */
 var $arRMONAcrescimo;
/**
     * @access Private
     * @var Object
 */
 var $obTARRGrupoCredito;
 /**
     * @access Private
     * @var Object
 */
 var $obTARRCreditoGrupo;
 /**

     * @access Private
     * @var Object
 */
 var $obTARRAcrescimoGrupo;
 /**
     * @access Private
     * @var Object
 */
 var $obRARRCalendarioFiscal;
 /**
     * @access Private
     * @var Ref Object
 */
 var $roUltimoAcrescimo;
 /**
     * @access Private
     * @var Ref Object
 */
 var $roUltimoCredito;

 // SETTERS

 function setFuncaoDesoneracao($valor) { $this->stFuncaoDesoneracao = $valor; }

 /**
     * @access Public
     * @param Integer $valor
 */
 function setCodGrupo($valor) { $this->inCodGrupo   = $valor    ; }
 /**
     * @access Public
     * @param String $valor
 */
 function setDescricao($valor) { $this->stDescricao  = $valor    ; }
 /**
     * @access Public
     * @param Integer $valor
 */
 function setCodModulo($valor) { $this->inCodModulo  = $valor    ; }
 /**
     * @access Public
     * @param String $valor
 */
 function setExercicio($valor) { $this->stExercicio  = $valor    ; }
 /**
     * @access Public
     * @param Integer $valor
 */
 function setCreditos($valor) { $this->arCreditos  = $valor    ; }
 /**
     * @access Public
     * @param String $valor
 */
 function setAcrescimos($valor) { $this->arAcrescimos = $valor    ; }

 // GETTERES
 /**
     * @access Public
     * @return Integer
 */
 function getCodGrupo() { return $this->inCodGrupo    ; }
 /**
     * @access Public
     * @return String
 */
 function getDescricao() { return $this->stDescricao   ; }
 /**
     * @access Public
     * @return Integer
 */
 function getCodModulo() { return $this->inCodModulo   ; }
 /**
     * @access Public
     * @return String
 */
 function getExercicio() { return $this->stExercicio   ; }
 function getFuncaoDesoneracao() { return $this->stFuncaoDesoneracao; }

 /**
      * Método construtor
      * @access Private
 */
 function RARRGrupo()
 {
     // mapeamento
     $this->obTARRGrupoCredito       = new TARRGrupoCredito      ;
     $this->obTARRPermissao          = new TARRPermissao         ;
     $this->obTARRCreditoGrupo       = new TARRCreditoGrupo      ;
     $this->obTARRAcrescimoGrupo     = new TARRAcrescimoGrupo    ;
     $this->obTARRArrecadacaoModulos = new TARRArrecadacaoModulos;
     // regras
     $this->obRMONAcrescimo         = new RMONAcrescimo         ;
     $this->obRMONCredito           = new RMONCredito           ;
     $this->obRARRCalendarioFiscal  = new RARRCalendarioFiscal  ;
     //
     $this->obTransacao          = new Transacao             ;
     //
     $this->arCreditos      = array();
     $this->arAcrescimos    = array();
     $this->arRMONCredito   = array();
     $this->arRMONAcrescimo = array();
     //
     $this->obRCadastroDinamico = new RCadastroDinamico             ;
     $this->obRCadastroDinamico->setPersistenteValores  ( new TARRAtributoGrupoValor );
//     $this->obRCadastroDinamico->setPersistenteAtributos( new TARRAtributoGrupo );
     $this->obRCadastroDinamico->setCodCadastro( 2 );
    // $this->obRCadastroDinamico->obRModulo->setCodModulo( 25 );
 }
 /**
     * Agrupa Créditos
     * @access Public
     * @param  Boolean $boTransacao
     * @return Object  $obErro
 */
function agruparCreditos($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $this->obRCadastroDinamico->setPersistenteAtributos( new TARRAtributoGrupo );
        $obErro = $this->obTARRGrupoCredito->proximoCod( $this->inCodGrupo, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            //inclusao em grupo_credito
            $this->obTARRGrupoCredito->setDado  ( "cod_grupo"       , $this->getCodGrupo () );
            $this->obTARRGrupoCredito->setDado  ( "cod_modulo"      , $this->getCodModulo() );
            $this->obTARRGrupoCredito->setDado  ( "ano_exercicio"   , $this->getExercicio() );
            $this->obTARRGrupoCredito->setDado  ( "descricao"       , $this->getDescricao() );
            $this->obTARRGrupoCredito->inclusao($boTransacao);
            //$this->obTARRGrupoCredito->debug();

            if ( $this->getFuncaoDesoneracao() ) {
                $arDesoneracao = explode( ".", $this->getFuncaoDesoneracao() );
                $obTARRRegraDesoneracaoGrupo = new TARRRegraDesoneracaoGrupo;
                $obTARRRegraDesoneracaoGrupo->setDado ( "cod_grupo", $this->getCodGrupo() );
                $obTARRRegraDesoneracaoGrupo->setDado ( "ano_exercicio", $this->getExercicio() );
                $obTARRRegraDesoneracaoGrupo->setDado ( "cod_modulo", $arDesoneracao[0] );
                $obTARRRegraDesoneracaoGrupo->setDado ( "cod_biblioteca", $arDesoneracao[1] );
                $obTARRRegraDesoneracaoGrupo->setDado ( "cod_funcao", $arDesoneracao[2] );
                $obTARRRegraDesoneracaoGrupo->inclusao ( $boTransacao );
            }

            if (!$obErro->ocorreu() ) {

                //O Restante dos valores vem setado da página de processamento
                $arChaveAtributo =  array( "cod_grupo"   => $this->getCodGrupo(), "ano_exercicio" => $this->getExercicio() );

                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );

                $obErro =  $this->obRCadastroDinamico->salvar ( $boTransacao );

                if ( !$obErro->ocorreu() ) {

                    // inclusao de acrescimo_grupo
                    $this->salvarAcrecimos('i',$boTransacao);

                    if ( !$obErro->ocorreu() ) {
                        // salvar creditos agrupados
                        $this->salvarCreditos('i',$boTransacao);
                    }
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCreditoGrupo );

    return $obErro;
 }
 /**
     * Altera Créditos agrupados
     * @access Public
     * @param  Object Transação
     * @return Object Erro
 */
function alteraGrupo($boTransacao = "")
{
    $boFlagTransacao = false;
     $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
     if ( !$obErro->ocorreu() ) {
         $this->obRCadastroDinamico->setPersistenteAtributos( new TARRAtributoGrupo );
         if ( !$obErro->ocorreu() ) {
             //inclusao em grupo_credito
             $this->obTARRGrupoCredito->setDado  ( "cod_grupo"       , $this->getCodGrupo () );
             $this->obTARRGrupoCredito->setDado  ( "cod_modulo"      , $this->getCodModulo() );
             $this->obTARRGrupoCredito->setDado  ( "ano_exercicio"   , $this->getExercicio() );
             $this->obTARRGrupoCredito->setDado  ( "descricao"       , $this->getDescricao() );
             $this->obTARRGrupoCredito->alteracao($boTransacao);

             if ( $this->getFuncaoDesoneracao() ) {
                 $arDesoneracao = explode( ".", $this->getFuncaoDesoneracao() );
                 $obTARRRegraDesoneracaoGrupo = new TARRRegraDesoneracaoGrupo;
                 $obTARRRegraDesoneracaoGrupo->setDado ( "cod_grupo", $this->getCodGrupo() );
                 $obTARRRegraDesoneracaoGrupo->setDado ( "ano_exercicio", $this->getExercicio() );
                 $obTARRRegraDesoneracaoGrupo->exclusao ( $boTransacao );
                 $obTARRRegraDesoneracaoGrupo->setDado ( "cod_modulo", $arDesoneracao[0] );
                 $obTARRRegraDesoneracaoGrupo->setDado ( "cod_biblioteca", $arDesoneracao[1] );
                 $obTARRRegraDesoneracaoGrupo->setDado ( "cod_funcao", $arDesoneracao[2] );
                 $obTARRRegraDesoneracaoGrupo->inclusao ( $boTransacao );
             }

             if (!$obErro->ocorreu() ) {
                 //O Restante dos valores vem setado da página de processamento
                 $arChaveAtributo =  array( "cod_grupo"   => $this->getCodGrupo(), "ano_exercicio" => $this->getExercicio() );
                 $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                 $obErro =  $this->obRCadastroDinamico->salvar ( $boTransacao );
                 if ( !$obErro->ocorreu() ) {
                     // inclusao de acrescimo_grupo
                     $this->salvarAcrecimos('a',$boTransacao);
                     if ( !$obErro->ocorreu() ) {
                         // salvar creditos agrupados
                         $this->salvarCreditos('a',$boTransacao);
                     }
                 }
             }
         }
     }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRCreditoGrupo );

    return $obErro;

}

 /**
     * Excluir grupo de credito
     * @access Public
     * @param  Object Transação
     * @return Object Erro
 */
function excluirGrupo($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $this->obRCadastroDinamico->setPersistenteAtributos( new TARRAtributoGrupo );
    if ( !$obErro->ocorreu() ) {
        // exclui acrescimos do grupo
        $obErro = $this->excluirAcrescimos( $boTransacao );
        if (!$obErro->ocorreu() ) {
            $obTARRRegraDesoneracaoGrupo = new TARRRegraDesoneracaoGrupo;
            $obTARRRegraDesoneracaoGrupo->setDado ( "cod_grupo", $this->getCodGrupo() );
            $obTARRRegraDesoneracaoGrupo->setDado ( "ano_exercicio", $this->getExercicio() );
            $obTARRRegraDesoneracaoGrupo->exclusao ( $boTransacao );

            // excluir creditos agrupados
            $this->obTARRCreditoGrupo->setDado ( "cod_grupo", $this->inCodGrupo );
            $this->obTARRCreditoGrupo->setDado ( "ano_exercicio", $this->getExercicio() );
            $obErro = $this->obTARRCreditoGrupo->exclusao( $boTransacao );
            if (!$obErro->ocorreu() ) {
                // excluir atributos selecionados
                $arChaveAtributo =  array( "cod_grupo"   => $this->getCodGrupo(), "ano_exercicio" => $this->getExercicio() );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
                $obErro =  $this->obRCadastroDinamico->excluir ( $boTransacao );
                if (!$obErro->ocorreu() ) {
                    $this->obRARRCalendarioFiscal->setCodigoGrupo( $this->getCodGrupo() );
                    $this->obRARRCalendarioFiscal->setAnoExercicio( $this->getExercicio() );
                    $this->obRARRCalendarioFiscal->addCalendarioGrupoVencimento();
                    $obErro = $this->obRARRCalendarioFiscal->excluirCalendario( $boTransacao );
                    if (!$obErro->ocorreu() ) {
                        // excluir permissoes
                        $this->obTARRPermissao->setDado( "ano_exercicio", $this->getExercicio() );
                        $this->obTARRPermissao->setDado( "cod_grupo", $this->getCodGrupo());
                        $obErro = $this->obTARRPermissao->exclusao( $boTransacao );
                        // finalmente, excluir grupo de credito
                        if (!$obErro->ocorreu() ) {
                            $this->obTARRGrupoCredito->setDado( "ano_exercicio", $this->getExercicio() );
                            $this->obTARRGrupoCredito->setDado( "cod_grupo", $this->inCodGrupo );
                            $obErro = $this->obTARRGrupoCredito->exclusao( $boTransacao );
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRGrupoCredito );

    return $obErro;
 }

 /**
     * Listagem de Modulos
     * @access Public
     * @param  Object Recordset
     * @param  Object Transação
     * @return Object Erro
 */
 function listarModulos(&$rsRecordset, $boTransacao = "")
 {
     $boFlagTransacao = false;
     $obErro = $this->obTARRArrecadacaoModulos->recuperaModulos($rsRecordset, $stFiltro, $stOrdem, $boTransacao );
 //    $this->obTARRArrecadacaoModulos->debug();
    return $obErro;
 }

 /**
     * Lista os Elementos segundo o filtro setado que não possuem calculos
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
 */
 function listarCalculoGrupos(&$rsRecordSet , $boTransacao = "")
 {
     $stFiltro = "";
     if ($this->inCodGrupo) {
         $stFiltro .= " acg.cod_grupo = ".$this->inCodGrupo." AND ";
     }
     if ($this->stDescricao) {
         $stFiltro .= " UPPER(acg.descricao) like UPPER('%".$this->stDescricao."%') AND ";
     }
     if ($this->stExercicio) {
         $stFiltro .= " acg.ano_exercicio = '".$this->stExercicio."' AND ";
     }
     if ($stFiltro) {
         $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro,0,-4)."";
     }

    ( $stFiltro ) ? $stFiltro.=" AND " : $stFiltro.= " WHERE ";

    $stFiltro.="  NOT EXISTS ( SELECT calculo_grupo_credito.cod_grupo                           \n";
    $stFiltro.="                 FROM arrecadacao.calculo_grupo_credito                         \n";
    $stFiltro.="                WHERE calculo_grupo_credito.cod_grupo     = acg.cod_grupo       \n";
    $stFiltro.="                  AND calculo_grupo_credito.ano_exercicio = acg.ano_exercicio ) \n";

    $stOrdem = " ORDER BY acg.cod_grupo ";

    $obErro = $this->obTARRGrupoCredito->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

 /**
     * Lista os Elementos segundo o filtro setado
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
 */
 function listarGrupos(&$rsRecordSet , $boTransacao = "")
 {
     $stFiltro = "";
     if ($this->inCodGrupo) {
         $stFiltro .= " acg.cod_grupo = ".$this->inCodGrupo." AND ";
     }
     if ($this->stDescricao) {
         $stFiltro .= " UPPER(acg.descricao) like UPPER('%".$this->stDescricao."%') AND ";
     }
     if ($this->stExercicio) {
         $stFiltro .= " acg.ano_exercicio = '".$this->stExercicio."' AND ";
     }
     if ($stFiltro) {
         $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro,0,-4)."";
     }
    $stOrdem = " ORDER BY acg.cod_grupo ";

    $obErro = $this->obTARRGrupoCredito->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}
 /**
     * Lista os Acrescimos do grupo
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
 */
 function listarAcrescimos(&$rsRecordSet , $boTransacao = "")
 {
     $stFiltro = "";
     if ($this->inCodGrupo) {
         $stFiltro .= "\r\n\t AND cod_grupo = ".$this->inCodGrupo."";
     }
     if ($this->inCodAcrescimo) {
         $stFiltro .= "\r\n\t AND cod_acrescimo = ".$this->inCodAcrescimo."";
     }
    $stOrdem = " ORDER BY cod_acrescimo ";

    $obErro = $this->obTARRAcrescimoGrupo->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
//    $this->obTARRAcrescimoGrupo->debug();
    return $obErro;
}
 /**
     * Lista os Creditos do grupo
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
 */
 function listarCreditos(&$rsRecordSet , $boTransacao = "")
 {
     $stFiltro = "";
     if ($this->inCodGrupo) {
         $stFiltro .= "\r\n\t acg.cod_grupo = ".$this->inCodGrupo." AND";
     }
     if ($this->stExercicio) {
         $stFiltro .= "\r\n\t acg.ano_exercicio = '".$this->stExercicio."' AND";
     }
     if ( $this->obRMONCredito->getCodCredito() ) {
         $stFiltro .= "\r\n\t acg.cod_credito = ".$this->obRMONCredito->getCodCredito()." AND";
     }
     if ( $this->obRMONCredito->getCodGenero() ) {
         $stFiltro .= "\r\n\t acg.cod_genero = ".$this->obRMONCredito->getCodGenero()." AND";
     }
     if ( $this->obRMONCredito->getCodEspecie() ) {
         $stFiltro .= "\r\n\t acg.cod_especie = ".$this->obRMONCredito->getCodEspecie()." AND";
     }
     if ( $this->obRMONCredito->getCodNatureza() ) {
         $stFiltro .= "\r\n\t acg.cod_natureza = ".$this->obRMONCredito->getCodNatureza()." AND ";
     }

     if ($stFiltro) {
         $stFiltro = "\r\n\t WHERE \r\n".substr($stFiltro,0,-4)."";
     }

    $stOrdem = " ORDER BY cod_credito ";

    $obErro = $this->obTARRCreditoGrupo->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    // $this->obTARRCreditoGrupo->debug();
    return $obErro;
}
 /**
     * Lista os Creditos do grupo COM FUNCAOES
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
 */
 function listarCreditosFuncao(&$rsRecordSet , $boTransacao = "")
 {
     $stFiltro = "";
     if ($this->inCodGrupo) {
         $stFiltro .= "\r\n\tAND a.cod_grupo = ".$this->inCodGrupo."";
     }
     if ( $this->obRMONCredito->getCodCredito() ) {
         //$stFiltro .= "\r\n\tAND c.cod_credito = ".$this->obRMONCredito->getCodCredito()."";
         $stFiltro .= "\r\n\tAND c.cod_credito in ( ".$this->obRMONCredito->getCodCredito()." )";
     }
     if ( $this->obRMONCredito->getCodGenero() ) {
         $stFiltro .= "\r\n\tAND c.cod_genero = ".$this->obRMONCredito->getCodGenero()."";
     }
     if ( $this->obRMONCredito->getCodEspecie() ) {
         $stFiltro .= "\r\n\tAND c.cod_especie = ".$this->obRMONCredito->getCodEspecie()."";
     }
     if ( $this->obRMONCredito->getCodNatureza() ) {
         $stFiltro .= "\r\n\tAND c.cod_natureza = ".$this->obRMONCredito->getCodNatureza()."";
     }
     if ($this->stExercicio) {
         $stFiltro .= "\r\n\tAND a.exercicio = '".$this->stExercicio."'";
     }
    $stOrdem = " ORDER BY a.cod_credito ";

    $obErro = $this->obTARRCreditoGrupo->recuperaCreditoFuncao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    // $this->obTARRCreditoGrupo->debug();
    return $obErro;
}

/**
     * Lista os Creditos COM FUNCOES
     * @access Public
     * @param  Object RecordSet
     * @param  Object Transação
     * @return Object Erro
 */
 function listarCreditosEspecificoFuncao(&$rsRecordSet , $boTransacao = "")
 {
    $stFiltro = "";
    if ( $this->obRMONCredito->getCodCredito() ) {
        $stFiltro .= "\r\n\tAND c.cod_credito = ".$this->obRMONCredito->getCodCredito()."";
    }
    if ( $this->obRMONCredito->getCodGenero() ) {
        $stFiltro .= "\r\n\tAND c.cod_genero = ".$this->obRMONCredito->getCodGenero()."";
    }
    if ( $this->obRMONCredito->getCodEspecie() ) {
        $stFiltro .= "\r\n\tAND c.cod_especie = ".$this->obRMONCredito->getCodEspecie()."";
    }
    if ( $this->obRMONCredito->getCodNatureza() ) {
        $stFiltro .= "\r\n\tAND c.cod_natureza = ".$this->obRMONCredito->getCodNatureza()."";
    }
    if ( $this->obRMONCredito->getDescricao() ) {
        $stFiltro .= " \r\n\tAND c.descricao_credito like '%".$this->obRMONCredito->getDescricao()."%'";
    }
    $stOrdem = " ORDER BY c.cod_credito ";
    $obErro = $this->obTARRCreditoGrupo->recuperaCreditoFuncao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Recupera do banco de dados os dados da Atividade selecionada
    * @access Public
    * @param  Object Transação
    * @return Object Erro
*/
function consultarGrupo($boTransacao = "")
{
    $obErro = new Erro;
    if ($this->inCodGrupo) {
        $obErro = $this->listarGrupos( $rsGrupos, $boTransacao );
        if ( !$obErro->ocorreu() && $rsGrupos->getNumLinhas() > 0 ) {
            $this->stDescricao          = $rsGrupos->getCampo( "descricao"          );
            $this->stExercicio          = $rsGrupos->getCampo( "ano_exercicio"      );
            $this->inCodModulo          = $rsGrupos->getCampo( "cod_modulo"         );
        } else {
            $this->inCodGrupo = '';
        }

    }

    return $obErro;
}

/**
* Salva os Creditos agrupados
* @access Public
*/
function salvarCreditos($stAcao='i', $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ($stAcao != 'i') {
            $this->obTARRCreditoGrupo->setDado ( "cod_grupo" , $this->getCodGrupo() );
            $this->obTARRCreditoGrupo->setDado ( "cod_credito" , $arCredito["codcredito"]  );
            $this->obTARRCreditoGrupo->setDado ( "cod_especie" , $arCredito["codespecie"]  );
            $this->obTARRCreditoGrupo->setDado ( "cod_genero"  , $arCredito["codgenero"]   );
            $this->obTARRCreditoGrupo->setDado ( "cod_natureza", $arCredito["codnatureza"] );
            $this->obTARRCreditoGrupo->setDado ( "ano_exercicio"    ,$this->getExercicio()     );
            $obErro = $this->obTARRCreditoGrupo->exclusao($boTransacao);
        }
        foreach ($this->arRMONCredito as $obRMONCredito) {
            $this->obTARRCreditoGrupo->setDado  ( "cod_grupo"   , $this->getCodGrupo() );
            $this->obTARRCreditoGrupo->setDado  ( "cod_credito" , $obRMONCredito->getCodCredito()   );
            $this->obTARRCreditoGrupo->setDado  ( "cod_especie" , $obRMONCredito->getCodEspecie()   );
            $this->obTARRCreditoGrupo->setDado  ( "cod_genero"  , $obRMONCredito->getCodGenero()    );
            $this->obTARRCreditoGrupo->setDado  ( "cod_natureza", $obRMONCredito->getCodNatureza()  );
            $this->obTARRCreditoGrupo->setDado  ( "ordem"		, $obRMONCredito->getOrdem()  		);
            if ( $obRMONCredito->getDesconto() == 'Sim' ) {
                $obRMONCredito->setDesconto( TRUE );
            } else {
                $obRMONCredito->setDesconto( FALSE );
            }
            $this->obTARRCreditoGrupo->setDado  ( "desconto"    , $obRMONCredito->getDesconto()     );
            $this->obTARRCreditoGrupo->setDado  ( "ano_exercicio"   , $this->getExercicio()             );
            $obErro = $this->obTARRCreditoGrupo->inclusao($boTransacao);
            //$this->obTARRCreditoGrupo->debug();
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}
/**
* Salva os Acrescimos do grupo
* @access Public
*/
function salvarAcrecimos($stAcao='i', $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $obErro = $this->excluirAcrescimos( $boTransacao );

        // insere os grupos selecionados
        foreach ($this->arRMONAcrescimo as $obRMONAcrescimo) {

            $this->obTARRAcrescimoGrupo->setDado ( "cod_acrescimo"  , $obRMONAcrescimo->getCodAcrescimo() );
            $this->obTARRAcrescimoGrupo->setDado ( "cod_tipo"  , $obRMONAcrescimo->getCodTipo() );
            $this->obTARRAcrescimoGrupo->setDado ( "cod_grupo"      , $this->getCodGrupo() );
            $this->obTARRAcrescimoGrupo->setDado ( "ano_exercicio"      , $this->getExercicio() );

            $obErro = $this->obTARRAcrescimoGrupo->inclusao($boTransacao);

           //$this->obTARRAcrescimoGrupo->debug();
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}
/**
* Exclui os Acrescimos do grupo
* @access Public
*/
function excluirAcrescimos($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $tmpChave = $this->obTARRAcrescimoGrupo->getComplementoChave();
        $tmpCod   = $this->obTARRAcrescimoGrupo->getCampoCod();
        $this->obTARRAcrescimoGrupo->setComplementoChave('cod_grupo');
        $this->obTARRAcrescimoGrupo->setCampoCod('');
        $this->obTARRAcrescimoGrupo->setDado("cod_grupo", $this->inCodGrupo );
        $this->obTARRAcrescimoGrupo->setDado("ano_exercicio", $this->getExercicio() );
        $obErro = $this->obTARRAcrescimoGrupo->exclusao( $boTransacao );
        $this->obTARRAcrescimoGrupo->setComplementoChave($tmpChave);
        $this->obTARRAcrescimoGrupo->setCampoCod($tmpCod);
   }

   return $obErro;
}

/**
*   Inclui novo Objeto de Acrescimo
*   @access Public
*/
function addAcrescimo()
{
    $this->arRMONAcrescimo[] = new RMONAcrescimo();
    $this->roUltimoAcrescimo = &$this->arRMONAcrescimo[ count($this->arRMONAcrescimo) - 1 ];
}
/**
*   Inclui novo Objeto de Crédito
*   @access Public
*/
function addCredito()
{
    $this->arRMONCredito[] = new RMONCredito();
    $this->roUltimoCredito = &$this->arRMONCredito[ count($this->arRMONCredito) - 1 ];
    $this->roUltimoCredito->refGrupoCredito($this);
}

function RecuperaMascaraGrupoCredito(&$stMascara , $boTransacao = "")
{
    $obErro = $this->obTARRGrupoCredito->recuperaMaxCodGrupo( $rsRecordSet, $boTransacao );
    if ( $rsRecordSet->getCampo("max_cod_grupo") ) {
        $stCodigo = $rsRecordSet->getCampo("max_cod_grupo");
        $stMascara = "";
        for ( $inX=0; $inX < strlen( $stCodigo ); $inX++ ) {
            $stMascara .= "9";
        }
    }

    return $obErro;
}

} // fecha classe

?>
