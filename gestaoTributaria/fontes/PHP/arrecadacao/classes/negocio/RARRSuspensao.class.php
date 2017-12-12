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
  * Página de Regra de Suspensão
  * Data de criação : 09/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @subpackage Regras
  * @package URBEM

    * $Id: RARRSuspensao.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.08
**/

/*
$Log$
Revision 1.17  2006/11/24 16:12:40  marson
Adição do caso de uso de Suspensão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRSuspensao.class.php"         );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRProcessoSuspensao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRSuspensaoTermino.class.php"  );
include_once ( CAM_GT_ARR_NEGOCIO."RARRTipoSuspensao.class.php"        );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php"              );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"               );
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php"              );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                     );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRGrupoCredito.class.php"      );

class RARRSuspensao
{
    /**
    * @access Private
    * @var Integer
    */
    public $inCGM;	// Cod_CGM
    /**
    * @access Private
    * @var Integer
    */
    public $inCodSuspensao;	// cod_suspensao
    /**
        * @access Private
        * @var Integer
    */
    public $inCodLancamento;   // cod_lancamento
    /**
        * @access Private
        * @var Integer
    */
    public $inCodGrupo;
    /**
        * @access Private
        * @var Date
    */
    public $dtInicio;          // inicio
    /**
        * @access Private
        * @var Date
    */
    public $dtTermino;         // termino
    /**
        * @access Private
        * @var String
    */
    public $stObservacao;      // observacoes

    /**
        * @access Private
        * @var String
    */
    public $stAnoExercicio;     // ano_exercicio

    /**
        * @access Private
        * @var Object
    */
    public $obRProcesso;

    /**
        * @access Private
        * @var Object
    */
    public $obRARRCalculo;

    /**
        * @access Private
        * @var Object
    */
    public $obTARRSuspensaoTermino;

    /**
        * @access Private
        * @var Object
    */
    public $obTARRProcessoSuspensao;

    /**
    * @access Private
    * @var Object
    */
    public $obRARRTipoSuspensao;

    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCGM($valor) { $this->inCGM           = $valor; }
    /**
        * @access Public
        * @param Integer $valor
    */
    public function setCodSuspensao($valor) { $this->inCodSuspensao           = $valor; }

    /**
        * @access Public
        * @param Integer valor
    */
    public function setCodLancamento($valor) { $this->inCodLancamento = $valor; }

    /**
        * @access Public
        * @param Integer valor
    */
    public function setCodGrupo($valor) { $this->inCodGrupo = $valor; }

    /**
        * @access Public
        * @param Date valor
    */
    public function setInicio($valor) { $this->dtInicio = $valor; }

    /**
        * @access Public
        * @param Date valor
    */
    public function setTermino($valor) { $this->dtTermino = $valor; }

    /**
        * @access Public
        * @param String valor
    */
    public function setObservacao($valor) { $this->stObservacao = $valor; }

    /**
        * @access Public
        * @param String valor
    */
    public function setAnoExercicio($valor) { $this->stAnoExercicio = $valor; }

    /**
        * @access Public
        * @return Integer
    */
    public function getCGM() { return $this->inCGM; }

    /**
        * @access Public
        * @return Integer
    */
    public function getCodSuspensao() { return $this->inCodSuspensao; }

    /**
        * @access Public
        * @return Integer
    */
    public function getCodLancamento() { return $this->inCodLancamento; }

    /**
        * @access Public
        * @param Integer valor
    */
    public function getCodGrupo() { return $this->inCodGrupo; }

    /**
        * @access Public
        * @return Date
    */
    public function getInicio() { return $this->dtInicio; }

    /**
        * @access Public
        * @return Date
    */
    public function getTermino() { return $this->dtTermino; }

    /**
        * @access Public
        * @return String
    */
    public function getObservacao() { return $this->stObservacao; }

    /**
        * @access Public
        * @return String
    */
    public function getAnoExercicio() { return $this->stAnoExercicio; }

    public function RARRSuspensao()
    {
        $this->obTARRSuspensao         = new TARRSuspensao;
        $this->obTARRSuspensaoTermino  = new TARRSuspensaoTermino;
        $this->obTARRProcessoSuspensao = new TARRProcessoSuspensao;
        $this->obTARRGrupoCredito      = new TARRGrupoCredito      ;  // Grupo de Crédito

        $this->obRARRCalculo           = new RARRCalculo;
        $this->obRProcesso             = new RProcesso;
        $this->obRMONCredito           = new RMONCredito           ;  // Crédito
        $this->obRARRTipoSuspensao     = new RARRTipoSuspensao     ;  // Tipo de Suspensão

        $this->obTransacao             = new Transacao;
    }
    /**
        * Suspender Crédito
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function suspenderCredito($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obTARRSuspensao->proximoCod( $this->inCodigoSuspensao, $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    $this->setCodSuspensao( $this->inCodigoSuspensao );
                    $this->obTARRSuspensao->setDado( "cod_suspensao"      , $this->getCodSuspensao() );
                    $this->obTARRSuspensao->setDado( "cod_tipo_suspensao" , $this->obRARRTipoSuspensao->getCodigoTipoSuspensao() );
                    $this->obTARRSuspensao->setDado( "inicio"             , $this->dtInicio );
                    $this->obTARRSuspensao->setDado( "observacoes"        , $this->getObservacao() );
                    $this->obTARRSuspensao->setDado( "cod_lancamento"     , $this->getCodLancamento() );
                    $obErro = $this->obTARRSuspensao->inclusao( $boTransacao );

                    if ( !$obErro->ocorreu() && $this->getTermino() ) {
                        $this->obTARRSuspensaoTermino->setDado( "cod_suspensao"  , $this->getCodSuspensao() );
                        $this->obTARRSuspensaoTermino->setDado( "termino"        , $this->dtTermino );
                        $this->obTARRSuspensaoTermino->setDado( "observacoes"    , $this->getObservacao() );
                        $this->obTARRSuspensaoTermino->setDado( "cod_lancamento" , $this->getCodLancamento() );
                        $obErro = $this->obTARRSuspensaoTermino->inclusao( $boTransacao );
                    }

                    if ( !$obErro->ocorreu() && $this->obRProcesso->getCodigoProcesso() ) {
                        $this->obTARRProcessoSuspensao->setDado( "cod_suspensao" , $this->getCodSuspensao() );
                        $this->obTARRProcessoSuspensao->setDado( "cod_processo"  , $this->obRProcesso->getCodigoProcesso() );
                        $this->obTARRProcessoSuspensao->setDado( "ano_exercicio" , $this->obRProcesso->getExercicio() );
                        $this->obTARRProcessoSuspensao->setDado( "cod_lancamento", $this->getCodLancamento() );
                        $obErro = $this->obTARRProcessoSuspensao->inclusao( $boTransacao );
                    }
                }
            }
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRSuspensao );

        return $obErro;
    }

    /**
        * Alterar Suspensão de Crédito
        * @access Public
        * @param  Object $obTransacao Parâmetro Transação
        * @return Object Objeto Erro
    */
    public function alterarSuspensao($boTransacao = "")
    {
        $boFlagTransacao = false;
        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->getTermino() ) {
                $this->obTARRSuspensaoTermino->setDado( "cod_suspensao"  , $this->getCodSuspensao() );
                $this->obTARRSuspensaoTermino->setDado( "termino"        , $this->getTermino() );
                $this->obTARRSuspensaoTermino->setDado( "observacoes"    , $this->getObservacao() );
                $this->obTARRSuspensaoTermino->setDado( "cod_lancamento" , $this->getCodLancamento() );
                $this->obTARRSuspensaoTermino->debug();
                $obErro = $this->obTARRSuspensaoTermino->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() && $this->obRProcesso->getCodigoProcesso() ) {
                  $this->obTARRProcessoSuspensao->setDado( "cod_suspensao" , $this->getCodSuspensao() );
                  $this->obTARRProcessoSuspensao->setDado( "cod_processo"  , $this->obRProcesso->getCodigoProcesso() );
                  $this->obTARRProcessoSuspensao->setDado( "ano_exercicio" , $this->obRProcesso->getExercicio() );
                  $this->obTARRProcessoSuspensao->setDado( "cod_lancamento", $this->getCodLancamento() );
                  $this->obTARRProcessoSuspensao->debug();
                  $obErro = $this->obTARRProcessoSuspensao->inclusao( $boTransacao );
                }
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTARRSuspensaoTermino );

        return $obErro;
    }

/**
    * Inclui os dados referentes ao lancamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
    public function listarSuspensaoLancamento(&$rsRecordset, $boTransacao = "")
    {
        // filtro
        $stFiltro = "";

          // CGM
          if ($this->getCGM()) {
             $stFiltro .= "\n\tcgm.numcgm = '".$this->getCGM()."' and";
          }

        // Grupo de Crédito
        if ($this->getCodGrupo()) {
           $stFiltro .= "\n\tacgc.cod_grupo || '/' || acgc.ano_exercicio = '".$this->getCodGrupo()."' and";
        }

          // Crédito
        if ($this->obRMONCredito->getCodCredito()) {
            $arCredito = explode('.',$this->obRMONCredito->getCodCredito());
           $stFiltro .= "\n\tac.cod_credito::int  = ".$arCredito[0]."::int and";
           $stFiltro .= "\n\tac.cod_especie::int  = ".$arCredito[1]."::int and";
           $stFiltro .= "\n\tac.cod_genero::int   = ".$arCredito[2]."::int and";
           $stFiltro .= "\n\tac.cod_natureza::int = ".$arCredito[3]."::int and";
        }

          if( $stFiltro )
            $stFiltro = "\n where ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

        // Exclui registro que já estáo no suspensão pois os mesmos não podem ser suspensos novamente
        $stFiltro .= "\n\tand NOT EXISTS (SELECT * \n\t\tFROM arrecadacao.suspensao \n\tWHERE arrecadacao.suspensao.cod_lancamento = alc.cod_lancamento)";

    // ordem
    $stOrdem    = "\n ORDER BY alc.cod_lancamento";

    // faz listagem
    $obErro = $this->obTARRSuspensao->selecionaSuspensaoLancamento($rsRecordset, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
    }
/**
    * Inclui os dados referentes a suspensao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
    public function listarSuspensao(&$rsRecordset, $boTransacao = "")
    {
        // filtro
        $stFiltro = "";

         // CGM
         if ($this->getCGM()) {
         $stFiltro .= "\n\tand cgm.numcgm = '".$this->getCGM()."'";
         }

        // Grupo de Crédito
       if ($this->getCodGrupo()) {
         $stFiltro .= "\n\tand acgc.cod_grupo || '/' || acgc.ano_exercicio = '".$this->getCodGrupo()."'";
        }

         // Crédito
        if ($this->obRMONCredito->getCodCredito()) {
           $stFiltro .= "\n\tand ac.cod_credito  = to_number( substring( '".$this->obRMONCredito->getCodCredito()."' from 1 for 3 ),'999')::int";
           $stFiltro .= "\n\tand ac.cod_especie  = to_number( substring( '".$this->obRMONCredito->getCodCredito()."' from 5 for 3 ),'999')::int";
           $stFiltro .= "\n\tand ac.cod_genero   = to_number( substring( '".$this->obRMONCredito->getCodCredito()."' from 9 for 2 ),'99')::int";
           $stFiltro .= "\n\tand ac.cod_natureza = to_number( substring( '".$this->obRMONCredito->getCodCredito()."' from 12 for 1 ),'9')::int";
        }

        // Tipo Suspensão
        if ($this->obRARRTipoSuspensao->getCodigoTipoSuspensao())
            $stFiltro .= "\n\tand suspensao.cod_tipo_suspensao = '".$this->obRARRTipoSuspensao->getCodigoTipoSuspensao()."'";

        // ordem
        $stOrdem    = "\n ORDER BY suspensao.cod_suspensao";

        // faz listagem
        $obErro = $this->obTARRSuspensao->recuperaRelacionamento($rsRecordset, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
    }
}
