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
    * Classe de Regra de Negócio Calendario
    * Data de Criação   : 09/08/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    $Revision: 30895 $
    $Name$
    $Author: tiago $
    $Date: 2007-06-20 15:16:28 -0300 (Qua, 20 Jun 2007) $

  * Casos de uso :uc-04.02.04
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioCadastro.class.php"                );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioFeriado.class.php"                   );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioFeriadoVariavel.class.php"           );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioFeriadoVariavel.class.php" );
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioPontoFacultativo.class.php");
include_once ( CAM_GRH_CAL_MAPEAMENTO."TCalendarioCalendarioDiaCompensado.class.php"   );
include_once ( CAM_GRH_CAL_NEGOCIO   ."RCalendarioFeriadoVariavel.class.php"           );
include_once ( CAM_GRH_CAL_NEGOCIO   ."RCalendarioPontoFacultativo.class.php"          );
include_once ( CAM_GRH_CAL_NEGOCIO   ."RCalendarioDiaCompensado.class.php"             );

/**
  * Classe de Regra de Negócio Calendario
  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Martins
*/
class RCalendario
{

  /**
    * @var Object
    * @access Private
  */
  public $obTCalendario;
  /**
    * @var Object
    * @access Private
  */
  public $obTCalendarioFeriado;

  /**
    * @var Object
    * @access Private
  */
  public $obTCalendarioCalendarioFeriadoVariavel;

  /**
    * @var Object
    * @access Private
  */
  public $obTCalendarioCalendarioPontoFacultativo;

  /**
    * @var Object
    * @access Private
  */
  public $obTCalendarioCalendarioDiaCompensado;

  /**
    * @var Integer
    * @access Private
  */
  public $inCodCalendar;

 /**
    * @var String
    * @access Private
  */
  public $stDescricao;

  /**
    * @var Object
    * @access Private
  */
  public $ultimoFeriadoVariavel;

  /**
    * @access Private
    * @var Array
  */
  public $arFeriadoVariavel;

  /**
    * @access Private
    * @var Array
  */
  public $arDiaCompensado;

  /**
    * @access Private
    * @var Array
  */
  public $arPontoFacultativo;

  /**
    * @access Public
    * @param Object $valor
  */
  public function setTCalendario($valor) { $this->obTCalendario = $valor; }

 /**
    * @access Public
    * @param Object $valor
  */
  public function setTCalendarioFeriado($valor) { $this->obTCalendarioFeriado = $valor; }

  /**
    * @access Public
    * @param Object $valor
  */
  public function setTCalendarioCalendarioFeriadoVariavel($valor)
  {
    $this->obTCalendarioCalendarioFeriadoVariavel = $valor;
  }

  /**
    * @access Public
    * @param Object $valor
  */
  public function setTCalendarioCalendarioPontoFacultativo($valor)
  {
    $this->obTCalendarioCalendarioPontoFacultativo = $valor;
  }

  /**
    * @access Public
    * @param Object $valor
  */
  public function setTCalendarioCalendarioDiaCompensado($valor)
  {
    $this->obTCalendarioCalendarioDiaCompensado = $valor;
  }

  /**
    * @access Public
    * @param Object $valor
  */
  public function setCodCalendar($valor)
  {
    $this->inCodCalendar = $valor;
  }

  /**
    * @access Public
    * @param Object $valor
  */
  public function setDescricao($valor)
  {
    $this->stDescricao = $valor;
  }

  /**
    * @access Public
    * @param Object $Valor
  */
  public function setTransacao($valor)
  {
    $this->obTransacao = $valor;
  }

  /**
    * @access Public
    * @param Object $Valor
  */
  public function setUltimoFeriadoVariavel($valor) {  $this->ultimoFeriadoVariavel = $valor;  }

  /**
    * @access Public
    * @param Object $Valor
  */
  public function setUltimoDiaCompensado($valor) {  $this->ultimoDiaCompensado = $valor;  }

  /**
    * @access Public
    * @param Object $Valor
  */
  public function setUltimoPontoFacultativo($valor) {  $this->ultimoPontoFacultativo = $valor;  }

  /**
    * @access Public
    * @param Object $Valor
  */
  public function setFeriadoVariavel($valor) {   $this->arFeriadoVariavel = $valor;  }

   /**
    * @access Public
    * @param Object $Valor
  */
  public function setDiaCompensado($valor) {   $this->arDiaCompensado = $valor;  }

 /**
    * @access Public
    * @param Object $Valor
  */
  public function setPontoFacultativo($valor) {   $this->arPontoFacultativo = $valor;  }

  /**
    * @access Public
    * @return Object
  */
  public function getTCalendario() {   return $this->obTCalendario;  }

  /**
    * @access Public
    * @return Object
  */
  public function getTCalendarioFeriado() {   return $this->obTCalendarioFeriado;  }

  /**
    * @access Public
    * @return Object
  */
  public function getTCalendarioCalendarioFeriadoVariavel()
  {
    return $this->obTCalendarioCalendarioFeriadoVariavel;
  }

  /**
    * @access Public
    * @return Object
  */
  public function getTCalendarioCalendarioPontoFacultativo()
  {
    return $this->obTCalendarioCalendarioPontoFacultativo;
  }

  /**
    * @access Public
    * @return Object
  */
  public function getTCalendarioCalendarioDiaCompensado()
  {
    return $this->obTCalendarioCalendarioDiaCompensado;
  }

  /**
    * @access Public
    * @param Object $valor
  */
  public function getCodCalendar()
  {
    return $this->inCodCalendar;
  }

  /**
    * @access Public
    * @param Object $valor
  */
  public function getDescricao()
  {
    return $this->stDescricao;
  }

  /**
    * @access Public
    * @return Object
  */
  public function getTransacao()
  {
    return $this->obTransacao;
  }

  /**
    * @access Public
    * @param Object $Valor
  */
  public function getUltimoFeriadoVariavel() { return $this->ultimoFeriadoVariavel;  }

  /**
    * @access Public
    * @param Object $Valor
  */
  public function getUltimoDiaCompensado() { return $this->ultimoDiaCompensado;  }

 /**
    * @access Public
    * @param Object $Valor
  */
  public function getUltimoPontoFacultativo() { return $this->ultimoPontoFacultativo;  }

  /**
    * @access Public
    * @param Object $Valor
  */
  public function getFeriadoVariavel() {    return $this->arFeriadoVariavel;  }

  /**
    * @access Public
    * @param Object $Valor
  */

  public function getDiaCompensado() {    return $this->arDiaCompensado;  }
 /**
    * @access Public
    * @param Object $Valor
  */
  public function getPontoFacultativo() {    return $this->arPontoFacultativo;  }

  /**
    * Instancia um novo objeto do Tipo Feriado Variavel
    * @access Public
  */
  public function addFeriadoVariavel() { $this->setUltimoFeriadoVariavel( new RFeriadoVariavel ); }

  /**
    * Instancia um novo objeto do Tipo Feriado Variavel
    * @access Public
  */
  public function addDiaCompensado() { $this->setUltimoDiaCompensado( new RCalendarioDiaCompensado ); }

  /**
    * Instancia um novo objeto do Tipo Feriado Variavel
    * @access Public
  */
  public function addPontoFacultativo() { $this->setUltimoPontoFacultativo( new RCalendarioPontoFacultativo ); }

  /**
    * Adiciona o objeto do tipo cargo ao array de cargos
    * @access Public
  */
  public function commitFeriadoVariavel()
  {
    $arFeriadoVariavel   = $this->getFeriadoVariavel();
    $arFeriadoVariavel[] = $this->getUltimoFeriadoVariavel();
    $this->setFeriadoVariavel( $arFeriadoVariavel );
  }
  /**
    * Adiciona o objeto do tipo cargo ao array de cargos
    * @access Public
  */
  public function commitDiaCompensado()
  {
    $arDiaCompensado   = $this->getDiaCompensado();
    $arDiaCompensado[] = $this->getUltimoDiaCompensado();
    $this->setDiaCompensado( $arDiaCompensado );
  }

 /**
    * Adiciona o objeto do tipo cargo ao array de cargos
    * @access Public
  */
  public function commitPontoFacultativo()
  {
    $arPontoFacultativo  = $this->getPontoFacultativo();
    $arPontoFacultativo[] = $this->getUltimoPontoFacultativo();
    $this->setPontoFacultativo( $arPontoFacultativo );
  }

  /**
    * Método Construtor
    * @access Private
  */
  public function RCalendario()
  {
    $this->setTCalendario                          ( new TCalendarioCalendarioCadastro      );
    $this->setTCalendarioFeriado                   ( new TCalendarioFeriado                   );
    $this->setTCalendarioCalendarioFeriadoVariavel ( new TCalendarioCalendarioFeriadoVariavel );
    $this->setTCalendarioCalendarioDiaCompensado   (new TCalendarioCalendarioDiaCompensado    );
    $this->setTCalendarioCalendarioPontoFacultativo(new TCalendarioCalendarioPontoFacultativo );
    $this->setTransacao                            ( new Transacao                            );
    $this->setFeriadoVariavel                      ( array() );
    $this->setPontoFacultativo                     ( array() );
    $this->setDiaCompensado                        ( array() );
  }

  /**
    * Salva Calendario no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function salvar($boTransacao = "")
  {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $this->getCodCalendar() != '0' ) {
        if ( !$obErro->ocorreu() ) {
          $this->obTCalendario->setDado("descricao"    , $this->getDescricao() );
          if ($this->getCodCalendar() ) {
            $this->obTCalendario->setDado("cod_calendar", $this->getCodCalendar() );
            $obErro = $this->obTCalendario->alteracao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
              $stChave = $this->obTCalendarioCalendarioFeriadoVariavel->getComplementoChave();
              $this->obTCalendarioCalendarioFeriadoVariavel->setComplementoChave('cod_calendar');
              $this->obTCalendarioCalendarioFeriadoVariavel->setDado( "cod_calendar",$this->getCodCalendar() );
              $obErro = $this->obTCalendarioCalendarioFeriadoVariavel->exclusao( $boTransacao );
              $this->obTCalendarioCalendarioFeriadoVariavel->setComplementoChave( $stChave );

              $stChave = $this->obTCalendarioCalendarioPontoFacultativo->getComplementoChave();
              $this->obTCalendarioCalendarioPontoFacultativo->setComplementoChave('cod_calendar');
              $this->obTCalendarioCalendarioPontoFacultativo->setDado( "cod_calendar",$this->getCodCalendar() );
              $obErro = $this->obTCalendarioCalendarioPontoFacultativo->exclusao( $boTransacao );
              $this->obTCalendarioCalendarioPontoFacultativo->setComplementoChave( $stChave );

              $stChave = $this->obTCalendarioCalendarioDiaCompensado->getComplementoChave();
              $this->obTCalendarioCalendarioDiaCompensado->setComplementoChave('cod_calendar');
              $this->obTCalendarioCalendarioDiaCompensado->setDado( "cod_calendar",$this->getCodCalendar() );
              $obErro = $this->obTCalendarioCalendarioDiaCompensado->exclusao( $boTransacao );
              $this->obTCalendarioCalendarioDiaCompensado->setComplementoChave( $stChave );
            }
          } else {
            $this->obTCalendario->proximoCod( $inCodCalendar , $boTransacao );
            $this->setCodCalendar( $inCodCalendar );
            $this->obTCalendario->setDado("cod_calendar", $this->getCodCalendar() );
            $obErro = $this->obTCalendario->inclusao( $boTransacao );
          }

          if ( !$obErro->ocorreu() ) {
            foreach ($this->arFeriadoVariavel as $obRFeriadoVariavel) {
              $this->obTCalendarioCalendarioFeriadoVariavel->setDado("cod_calendar", $this->getCodCalendar() );
              $this->obTCalendarioCalendarioFeriadoVariavel->setDado("cod_feriado" , $obRFeriadoVariavel->getCodFeriado() );
              $obErro = $this->obTCalendarioCalendarioFeriadoVariavel->inclusao( $boTransacao );
              if( $obErro->ocorreu() )
                        break;
            }
            foreach ($this->arPontoFacultativo as $obRPontoFacultativo) {
              $this->obTCalendarioCalendarioPontoFacultativo->setDado("cod_calendar", $this->getCodCalendar() );
              $this->obTCalendarioCalendarioPontoFacultativo->setDado("cod_feriado" , $obRPontoFacultativo->getCodFeriado() );
              $obErro = $this->obTCalendarioCalendarioPontoFacultativo->inclusao( $boTransacao );
              if( $obErro->ocorreu() )
                        break;
            }
            foreach ($this->arDiaCompensado as $obRDiaCompensado) {
              $this->obTCalendarioCalendarioDiaCompensado->setDado("cod_calendar", $this->getCodCalendar() );
              $this->obTCalendarioCalendarioDiaCompensado->setDado("cod_feriado" , $obRDiaCompensado->getCodFeriado() );
              $obErro = $this->obTCalendarioCalendarioDiaCompensado->inclusao( $boTransacao );
              if( $obErro->ocorreu() )
                        break;
            }

          }
      }
      } else {
        $obErro->setDescricao("Você não pode efetuar alterações neste calendário!");
      }
      $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro,
      $this->obTCalendario );

      return $obErro;
}

  /**
    * Exclui dados de Calendario do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function excluir($boTransacao = "")
  {
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $this->getCodCalendar() != '0' ) {
        if ( !$obErro->ocorreu() ) {
           $this->obTCalendarioCalendarioDiaCompensado->setDado("cod_calendar",$this->getCodCalendar());
           $boErro = $this->obTCalendarioCalendarioDiaCompensado->exclusao($boTransacao);
           if ( !$obErro->ocorreu() ) {
              $this->obTCalendarioCalendarioFeriadoVariavel->setDado("cod_calendar",$this->getCodCalendar());
              $boErro = $this->obTCalendarioCalendarioFeriadoVariavel->exclusao($boTransacao);
              if ( !$obErro->ocorreu() ) {
                 $this->obTCalendarioCalendarioPontoFacultativo->setDado("cod_calendar",$this->getCodCalendar());
                 $boErro = $this->obTCalendarioCalendarioPontoFacultativo->exclusao($boTransacao);
                 if ( !$obErro->ocorreu() ) {
                     $this->obTCalendario->setDado("cod_calendar", $this->getCodCalendar());
                     $obErro = $this->obTCalendario->exclusao( $boTransacao );
                  }
              }
           }
        }
    } else {
        $obErro->setDescricao("Você não pode excluir este calendário!");
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCalendario );

    return $obErro;
  }

  /**
    * Lista todos os Calendarios de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function listar(&$rsLista, $stOrder = "", $obTransacao = "")
  {
    $stFiltro = "";
    if( $this->stDescricao )
      $stFiltro .= " AND LOWER(descricao) LIKE LOWER('%".$this->stDescricao."%') ";

    $stFiltro = ($stFiltro)?' WHERE cod_calendar IS NOT NULL '.$stFiltro:'';

    $obErro = $this->obTCalendario->recuperaTodos( $rsLista, $stFiltro, $stOrder,$obTransacao );

    return $obErro;
  }

  /**
    * Lista todos os Feriados de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function listarFeriados(&$rsLista, $stOrder = "" , $boTransacao = "")
  {
    if ( $this->getCodCalendar() != "" ) {
      $this->obTCalendario->setDado('cod_calendar', $this->getCodCalendar() );
    }

    if ($this->ultimoFeriadoVariavel->getDtInicial()) {
        $this->obTCalendario->setDado('dt_inicial',$this->ultimoFeriadoVariavel->getDtInicial());

    }
    if ($this->ultimoFeriadoVariavel->getDtFinal()) {
        $this->obTCalendario->setDado('dt_final',$this->ultimoFeriadoVariavel->getDtFinal());
    }

    if ($this->ultimoFeriadoVariavel->getDtFeriado()) {
        $this->obTCalendario->setDado('dt_feriado',$this->ultimoFeriadoVariavel->getDtFeriado());
    }

    $obErro = $this->obTCalendario->recuperaRelacionamento($rsLista,$stFiltro,"",$boTransacao);

    return $obErro;
  }

  /**
    * Lista todos os Feriados Disponíveis para o Calendarios selecionado
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function listarFeriadosDisponiveis(&$rsLista, $stOrder = "dt_feriado",$obTransacao = "")
  {
    $stFiltro = "";
    $this->obTCalendarioFeriado->setDado("ano", date("Y"));
    if ($this->getCodCalendar() != "") {
       $this->obTCalendarioFeriado->setDado( "cod_calendar", $this->getCodCalendar() );

     $stFiltro .= "    AND f.cod_feriado  not in(
                    SELECT cod_feriado
                      FROM calendario.calendario_feriado_variavel
                     WHERE f.cod_feriado = cod_feriado
                       AND cod_calendar = ".$this->getCodCalendar().")";

     $stFiltro .= "    AND f.cod_feriado  not in(
                    SELECT cod_feriado
                      FROM calendario.calendario_ponto_facultativo
                     WHERE f.cod_feriado = cod_feriado
                       AND cod_calendar = ".$this->getCodCalendar().")";

     $stFiltro .= "    AND f.cod_feriado  not in(
                    SELECT cod_feriado
                      FROM calendario.calendario_dia_compensado
                     WHERE f.cod_feriado = cod_feriado
                       AND cod_calendar = ".$this->getCodCalendar().")";
    }
    $stFiltro .= "   AND tipoferiado <> 'F' ";
    $obErro = $this->obTCalendarioFeriado->recuperaRelacionamento($rsLista,$stFiltro,$stOrder,$obTransacao);

    return $obErro;
  }

  /**
    * Lista todos os Feriados Selecionados para o Calendarios selecionado
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function listarFeriadosSelecionados(&$rsLista, $stOrder = "dt_feriado",$obTransacao = "")
  {
    $stFiltro = "";
    $this->obTCalendarioFeriado->setDado("ano", date("Y"));
    if ($this->getCodCalendar() != "") {
       $this->obTCalendarioFeriado->setDado( "cod_calendar", $this->getCodCalendar() );

     $stFiltro .= "    AND f.cod_feriado  in(
                    SELECT cod_feriado
                      FROM calendario.calendario_feriado_variavel
                     WHERE f.cod_feriado = cod_feriado
                       AND cod_calendar = ".$this->getCodCalendar().")";

     $stFiltro .= "     OR f.cod_feriado  in(
                    SELECT cod_feriado
                      FROM calendario.calendario_ponto_facultativo
                     WHERE f.cod_feriado = cod_feriado
                       AND cod_calendar = ".$this->getCodCalendar().")";

     $stFiltro .= "     OR f.cod_feriado  in(
                    SELECT cod_feriado
                      FROM calendario.calendario_dia_compensado
                     WHERE f.cod_feriado = cod_feriado
                       AND cod_calendar = ".$this->getCodCalendar().")";
    }
    $stFiltro .= "   AND tipoferiado <> 'F' ";
    $obErro = $this->obTCalendarioFeriado->recuperaRelacionamento($rsLista,$stFiltro,$stOrder,$obTransacao);

    return $obErro;
  }

  /**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
  */
  public function consultar($boTransacao = "")
  {
    $this->obTCalendario->setDado( "cod_calendar" , $this->getCodCalendar() );
    $obErro = $this->obTCalendario->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
      $this->setDescricao   ( $rsRecordSet->getCampo("descricao") );
    }

    return $obErro;
  }

}
