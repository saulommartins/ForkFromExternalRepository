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
/*
    * Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 25/08/2006

    * @author Analista      : Cleisson
    * @author Desenvolvedor : Rodrigo

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor:$
    $Date: 2008-03-04 09:28:44 -0300 (Ter, 04 Mar 2008) $

    * Casos de uso: uc-02.01.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                       );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"                                );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"                           );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoReceita.class.php"                        );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoOrcamentaria.class.php"                   );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php"                 );

/**
 * METAS
 */

class RRelatorioMetasArrecadacaoReceita extends PersistenteRelatorio
{
var $stCodReceitaDedutoraInicial;
var $stCodReceitaDedutoraFinal;
/**
     * @access Public
     * @param String $valor
*/
function setEntidade($valor) { $this->stEntidade   = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio  = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setSimNao($valor) { $this->inSimNao     = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEstruturalInicial($valor) { $this->stCodEstruturalInicial = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEstruturalFinal($valor) { $this->stCodEstruturalFinal = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodRecurso($valor) { $this->inCodRecurso = $valor; }
function setDestinacaoRecurso($valor) { $this->stDestinacaoRecurso = $valor; }
function setCodDetalhamento($valor) { $this->inCodDetalhamento = $valor; }
function setCodReceitaDedutoraInicial($valor) { $this->stCodReceitaDedutoraInicial = $valor; }
function setCodReceitaDedutoraFinal($valor) { $this->stCodReceitaDedutoraFinal = $valor; }

/*
    * @access Public
    * @return String
*/
function getEntidade() { return $this->stEntidade;               }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;             }
/**
     * @access Public
     * @return Integer
*/
function getSimNao() { return $this->inSimNao;             }
/**
     * @access Public
     * @return String
*/
function getCodEstruturalInicial() { return $this->stCodEstruturalInicial;    }
/**
     * @access Public
     * @return String
*/
function getCodEstruturalFinal() { return $this->stCodEstruturalFinal;    }
/**
     * @access Public
     * @return Integer
*/
function getCodRecurso() { return $this->inCodRecurso;    }
function getDestinacaoRecurso() { return $this->stDestinacaoRecurso; }
function getCodDetalhamento() { return $this->inCodDetalhamento; }
function getCodReceitaDedutoraInicial() { return $this->stCodReceitaDedutoraInicial; }
function getCodReceitaDedutoraFinal() { return $this->stCodReceitaDedutoraFinal; }

  public function RRelatorioMetasArrecadacaoReceita()
  {
      $this->obRRelatorio = new RRelatorio;
      $this->stEntidade   = $this->stEntidade;
      $this->stExercicio  = $this->stExercicio;
  }

  public function geraRecordSet(&$rsRecordSet , $stOrder = "")
  {
      $obROrcamentoReceita              = new ROrcamentoReceita;
      $obRContablidadeLancamentoReceita = new RContabilidadeLancamentoReceita;
      $obRConfiguracaoOrcamento         = new ROrcamentoConfiguracao;
      $obRPrevisaoReceita               = new ROrcamentoPrevisaoReceita;

      $obRConfiguracaoOrcamento->setExercicio($this->stExercicio);
      $obRConfiguracaoOrcamento->consultarConfiguracao();
      if (Sessao::getExercicio() < '2014') {
          $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetas();
      } else {
          $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetasReceita();
      }

      if ($inUnidadesMedidasMetas > 0) {
        $inNumeroColunas = (12/$inUnidadesMedidasMetas);
      }

      $obRContablidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio($this->stExercicio);
      $obRContablidadeLancamentoReceita->consultarExistenciaReceita();
      $arBloco1 = array();
      $inCount  = 0;

        if (is_array($this->stEntidade)) {
          foreach ($this->stEntidade as $key => $valor) {

              $obROrcamentoReceita->setExercicio($this->stExercicio);
              $obErro = $obROrcamentoReceita->consultar( $rsReceita );
              if ( !$obErro->ocorreu() ) {
                 $inCodRecurso    = $rsReceita->getCampo( 'cod_recurso'      );
                 $stDescricao     = $rsReceita->getCampo( 'descricao'        );
                 $nuValorOriginal = $rsReceita->getCampo( 'vl_original'      );
                 $nuValorOriginal = number_format( $nuValorOriginal,2,',','.');
                 /*
                  * CONSULTAR METAS
                  */
                  $stCondicao = "   AND receita.cod_entidade    = ".$valor."                                     \n";
                  $stCondicao.= "   AND receita.exercicio       = '".$this->stExercicio."'                       \n";
                  if ($this->inCodRecurso!="") {
                     $stCondicao.= "   AND receita.cod_recurso  = ".$this->inCodRecurso."                        \n";
                  }
                  if ($this->stDestinacaoRecurso != "") {
                     $stCondicao .= " AND recurso.masc_recurso_red like '".$this->stDestinacaoRecurso."%'";
                  }
                  if ($this->inCodDetalhamento != "") {
                     $stCondicao .= " AND recurso.cod_detalhamento = ".$this->inCodDetalhamento;
                  }
                  if (!($this->stCodEstruturalInicial=="" And $this->stCodEstruturalFinal=="")) {
                     if ($this->stCodEstruturalFinal=="") {
                         $stCondicao.= " AND conta_receita.cod_estrutural ilike publico.fn_mascarareduzida('".$this->stCodEstruturalInicial."')||'%' \n";
                     } else {
                         $stCondicao.= " AND (conta_receita.cod_estrutural BETWEEN '".$this->stCodEstruturalInicial."'\n";
                         $stCondicao.= " AND '".$this->stCodEstruturalFinal."')                                       \n";
                     }
                  }

                  if ($this->stCodEstruturalInicial == "" && $this->stCodEstruturalFinal == "") {
                      if (!($this->stCodReceitaDedutoraInicial=="" And $this->stCodReceitaDedutoraFinal=="")) {
                         if ($this->stCodReceitaDedutoraFinal=="") {
                             $stCondicao.= " AND conta_receita.cod_estrutural ilike publico.fn_mascarareduzida('".$this->stCodReceitaDedutoraInicial."')||'%' \n";
                         } else {
                             $stCondicao.= " AND (conta_receita.cod_estrutural BETWEEN '".$this->stCodReceitaDedutoraInicial."'\n";
                             $stCondicao.= " AND '".$this->stCodReceitaDedutoraFinal."')                                       \n";
                         }
                      }
                  }

                  if ($this->inSimNao=="N") {
                    $obErro = $obROrcamentoReceita->recuperaEntidadeAnalitica($rsRecurso,$stCondicao);
                  } elseif ($this->inSimNao=="S") {
                    $obErro = $obROrcamentoReceita->recuperaEntidadeSintetica($rsRecurso,$stCondicao);
                  }

                  while (!$rsRecurso->eof()) {
                      $obROrcamentoReceita->setCodReceita($rsRecurso->getCampo( 'cod_receita'));
                      $stDescricao        = $rsRecurso->getCampo('descricao'                  );
                      $stEstrutura        = $rsRecurso->getCampo('cod_estrutural'             );
                      $obRPrevisaoReceita->obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade($valor);
                      $stFiltro = " cod_receita=".$obROrcamentoReceita->getCodReceita();
                      $obRPrevisaoReceita->listarPeriodo($rsListaReceita2,$stFiltro."");

                      $arMetas[$rsRecurso->getCampo('cod_conta')]['descricao']                       = $rsRecurso->getCampo('descricao');
                      $arMetas[$rsRecurso->getCampo('cod_conta')]['cod_estrutural']                  = $rsRecurso->getCampo('cod_estrutural');
                      $arMetas[$rsRecurso->getCampo('cod_conta')][$rsRecurso->getCampo( 'periodo' )] = $rsRecurso->getCampo('vl_periodo');

                      $inCount++;
                      $rsRecurso->proximo();

                 }
             }
         }
      }
      $arDados = array();
      if (count($arMetas)>0) {
          foreach ($arMetas as $indice => $arLinha) {
              $arMetas[$indice]['totalAno'] = 0;
            for ($inCont= 1 ; $inCont <= $inNumeroColunas; $inCont++) {
               $arMetas[$indice]['totalAno']=$arMetas[$indice]['totalAno'] + $arMetas[$indice][$inCont];
            }
            $arDados[] = $arMetas[$indice];
          }
      }

      $rsBloco1  = new RecordSet;
      $rsBloco1->preenche($arDados);
      $rsBloco1->addFormatacao( 'totalAno', 'NUMERIC_BR' );

      /// formatando as outras colunas
      Sessao::write('periodos',$inNumeroColunas);
      //sessao->transf['periodos'] = $inNumeroColunas;

      for ($inPos = 1 ; $inPos <= $inNumeroColunas ; $inPos++) {
            $rsBloco1->addFormatacao( $inPos, 'NUMERIC_BR' );
      }

      $rsRecordSet = array( 0=>$rsBloco1 );

      return $obErro;
   }
}
?>
