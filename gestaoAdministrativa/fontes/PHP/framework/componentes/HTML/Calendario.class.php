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
* Gerar o a exibição de um calendário, conforme um período
* Data de Criação: 05/08/2004

* @author Desenvolvedor: Eduardo Martins

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que monta o HTML do Calendario é uma extensão de Formulario
    * @author Desenvolvedor: Eduardo Martins
*/
class Calendario extends Formulario
{
/**
    * @access Private
    * @var String
*/
var $stData;

/**
    * @access Private
    * @var RecordSet
*/
var $rsFeriados;

/**
    * @access Private
    * @var String
*/
var $stLinkDescricao;

/**
    * Método Construtor
    * @access Public
*/
function Calendario()
{
    parent::Formulario();
    $this->stTarget          = 0;
    $this->stLink            = $_SERVER['PHP_SELF'];
    $this->stComplementoLink = '';
}

/**
    * @access Public
    * @param String $valor
*/
function setTarget($valor) { $this->stTarget          = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setLink($valor) { $this->stLink            = $valor; }
/**
    * @access Public
    * @param RecordSet $valor
*/
function setRsFeriados($valor) { $this->rsFeriados        = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setLinkDescricao($valor) { $this->stLinkDescricao   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setComplementoLink($valor) { $this->stComplementoLink = $valor; }

/**
    * @access Public
    * @return String
*/
function getTarget() { return $this->stTarget;           }
/**
    * @access Public
    * @return String
*/
function getLink() { return $this->stLink  ;           }
/**
    * @access Public
    * @return RecordSet
*/
function getRsFeriados() { return $this->rsFeriados  ;       }

/**
    * @access Public
    * @return String
*/
function getComplementoLink() { return $this->stComplementoLink;  }

/**
    * FALTA DESCRICAO
    * @access Private
    * @param Integer $inMes
    * @param Integer $inAno
    * @return String
*/
function retornaPrimDiaSemana($inMes, $inAno)
{
    return  date( "w", mktime( 0,0,0,$inMes, 1, $inAno ) );
}

/**
    * FALTA DESCRICAO
    * @access Private
    * @param Integer $inDia
    * @param Integer $inMes
    * @param Integer $inAno
    * @return String
*/
function retornaDiaSemana($inDia, $inMes, $inAno)
{
    return  date( "w", mktime( 0,0,0,$inMes, $inDia, $inAno ) );
}

/**
    * FALTA DESCRICAO
    * @access Private
    * @param Integer $inDia
    * @param Integer $inMes
    * @param Integer $inAno
    * @return Boolean
*/
function verificaHoje($inDia, $inMes, $inAno)
{
  $retorno = false;
  if (date( "d/m/Y", mktime( 0,0,0,$inMes, $inDia, $inAno ) ) == date("d/m/Y") ) {
      $retorno = true;
  }

  return $retorno;
}

/**
    * FALTA DESCRICAO
    * @access Private
    * @param Integer $inMes
    * @param Integer $inAno
    * @return String
*/
function retornaUltimoDiaMes($inMes, $inAno)
{
    $inTime = mktime( 0,0,0,$inMes + 1 , 1, $inAno ) - (24 * 60 * 60);

    return  date( "d", $inTime );
}

/**
    * FALTA DESCRICAO
    * @access Private
    * @param Strign $stData
    * @return String
*/
function retornaEstilo($stData)
{
  $arData = explode( "/", $stData );
  $retorno = 'dia';
  $this->setLinkDescricao( "" );

  if ( $this->retornaDiaSemana( $arData[0], $arData[1], $arData[2] ) == 0 ) {
      $retorno = 'labelDia';
  }

  while ( !$this->rsFeriados->eof() ) {

    $stLinkDescricao .= $this->rsFeriados->getCampo( "descricao" ) . " ";
    $stTipo           = $this->rsFeriados->getCampo( "tipoferiado" );
    $stDataCompleta   = $this->rsFeriados->getCampo( "dt_feriado" );
    $stTipoCor        = $this->rsFeriados->getCampo( "tipo_cor");
    $stDiaMes         = substr( $stDataCompleta , 0 , 5 );

    $arTipoCor = explode("-",$stTipoCor);
    /*
       D - dia compensado      = 3
       F - feriado fixo        = 7
       P - ponto facultativo   = 9
       V - feriado variável    = 11
    */

    if ($stDiaMes == substr( $stData, 0, 5 )) {
      if (($stDataCompleta == $stData)) {
              $inTipoCor = 0;
              for ($inCount = 1; $inCount <= count($arTipoCor) ; $inCount++) {
                 if (($arTipoCor[$inCount])=='D') {
                     $inTipoCor = bcadd($inTipoCor,3);
                     }
                   if (($arTipoCor[$inCount])=='F') {
                       $inTipoCor = bcadd($inTipoCor,7);
                   }
                     if (($arTipoCor[$inCount])=='P') {
                         $inTipoCor = bcadd($inTipoCor,9);
                     }
                       if (($arTipoCor[$inCount])=='V') {
                           $inTipoCor = bcadd($inTipoCor,11);
                       }
              }
      }

      switch ($inTipoCor) {
         case "3":
           $retorno = 'feriadodiacompensado';
         break;
         case "7":
           $retorno = 'feriadofixo';
         break;
         case "9":
           $retorno = 'feriadopontofacultativo';
         break;
         case "11":
           $retorno = 'feriadovariavel';
         break;
         case "10":
           $retorno = 'feriadodf';// DF ou FD
         break;
         case "12":
           $retorno = 'feriadodp';//DP ou PD
         break;
         case "14":
           $retorno = 'feriadodv';//DV ou VD
         break;
         case "16":
           $retorno = 'feriadofp';//FP ou PF
         break;
         case "18":
           $retorno = 'feriadofv';//FV ou VF
         break;
         case "20":
           $retorno = 'feriadopv';//PV ou VP
         break;
         case "19":
           $retorno = 'feriadodfp';//DFP
         break;
         case "21":
           $retorno = 'feriadodfv';//DFV
         break;
         case "23":
           $retorno = 'feriadodpv';//DPV
         break;
         case "27":
           $retorno = 'feriadofpv';//FPV
         break;
         case "30":
           $retorno = 'feriadodfpv';//DFPV
         break;
      }

    }

    $this->rsFeriados->proximo();
  }
  $this->rsFeriados->setPrimeiroElemento();

  return $retorno;
}

/**
    * Monta um mês do Calendário
    * @access Protected
    * @param Integer $mes
    * @param Integer $ano
    * @return String
*/
function listaMes($mes, $ano)
{
    $arSemana = array ("D","S","T","Q","Q","S","S");

    $arMes   = array ("Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril",   "Maio",     "Junho",
                       "Julho",   "Agosto",    "Setembro",     "Outubro", "Novembro", "Dezembro");

    $iPrimDia   = $this->retornaPrimDiaSemana( $mes, $ano );
    $iTotalDias = $this->retornaUltimoDiaMes( $mes, $ano );
    $inContDiaMes = 1;
    $inContSemana = 0;

    $obTabela = new Tabela;

    $obTabela->setWidth("");

    //imprime nome do Mes
    $obTabela->addLinha();
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setColSpan("7");
    $obTabela->ultimaLinha->ultimaCelula->setClass( "labelcentercabecalho" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( $arMes[ $mes-1 ] );
    $obTabela->ultimaLinha->commitCelula();
    $obTabela->commitLinha();

    //imprime dias da semana
    $obTabela->addLinha();
    foreach ($arSemana as $stDiaSemana) {
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "labelcenter" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stDiaSemana );
        $obTabela->ultimaLinha->commitCelula();
    }
    $obTabela->commitLinha();

    //imprime dias do mes
    $obTabela->addLinha();
    for ($i = 1 ; $i <= 42 ; $i++) {
        //imprime os dias de cada mes
        if ($i <= $iPrimDia  or  $inContDiaMes > $iTotalDias) {

          if ($inContSemana == 0) {
            $Estilo = "labelDia";
          } else {
            $Estilo = "fieldcalendario";
          }

            $obTabela->ultimaLinha->addCelula();
            $obTabela->ultimaLinha->ultimaCelula->setClass( $Estilo );
            $obTabela->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
            $obTabela->ultimaLinha->commitCelula();

        } else {

          $stData = str_pad($inContDiaMes,2,"0",STR_PAD_LEFT).'/'.str_pad($mes,2,"0",STR_PAD_LEFT).'/'.$ano;

          $Estilo = $this->retornaEstilo( $stData );

          $obLink = new Link;
          $obLink->setValue ( "&nbsp;" . $inContDiaMes );
//          $obLink->setLinkTitle( $this->getLinkDescricao() );
          $obLink->setHRef  ( $this->stLink .'?'. Sessao::getId() ."&dtData=" . $stData . $this->stComplementoLink );

          $inContDiaMes++;
          if( $this->stTarget )
            $obLink->setTarget( $this->stTarget );

          $obTabela->ultimaLinha->addCelula();
          $obTabela->ultimaLinha->ultimaCelula->setClass( $Estilo );
          $obTabela->ultimaLinha->ultimaCelula->addComponente( $obLink );
          $obTabela->ultimaLinha->commitCelula();
        }

        $inContSemana++;

        if ($inContSemana > 6) {
            $obTabela->commitLinha();
            $obTabela->addLinha();
            $inContSemana = 0;
      }
    }

    $obTabela->montaHtml();
    $this->setHtml( $obTabela->getHtml() );

    return $this->getHtml();
}

/**
    * Monta o Calendário com 12 Meses
    * @access Public
    * @param Integer $ano
*/
function montaCalendario($ano)
{
  $obTabelaAno = new Tabela;

  $iContaColuna = 1;
  $obTabelaAno->setWidth("");
  $obTabelaAno->setAlign("CENTER");
  $obTabelaAno->setCellSpacing("2");
  $obTabelaAno->setCellPadding("2");

  //imprime o ano
  $obTabelaAno->addLinha();
  $obTabelaAno->ultimaLinha->addCelula();
  $obTabelaAno->ultimaLinha->ultimaCelula->setColSpan("4");
  $obTabelaAno->ultimaLinha->ultimaCelula->setClass( "labelcenterCalendario" );
  $obTabelaAno->ultimaLinha->ultimaCelula->addConteudo( $ano  );
  $obTabelaAno->ultimaLinha->commitCelula();
  $obTabelaAno->commitLinha();

  $obTabelaAno->addLinha();
  for ($i = 1; $i <= 12; $i++) {

    $obTabelaAno->ultimaLinha->addCelula();
    $obTabelaAno->ultimaLinha->ultimaCelula->addConteudo( $this->listaMes( $i, $ano ) );
    $obTabelaAno->ultimaLinha->commitCelula();

    if ($iContaColuna == 4) {
      $iContaColuna = 0;
      $obTabelaAno->commitLinha();
      $obTabelaAno->addLinha();
    }

    $iContaColuna++;
  }

  $stBranco = "&nbsp&nbsp&nbsp&nbsp";
  $obTabelaLegenda = new Tabela;
  $obTabelaLegenda->setWidth("100");
  $obTabelaLegenda->addLinha();
  $obTabelaLegenda->ultimaLinha->addCelula();
  $obTabelaLegenda->ultimaLinha->ultimaCelula->setClass( "feriadofixo" );
  $obTabelaLegenda->ultimaLinha->ultimaCelula->addConteudo($stBranco);
  $obTabelaLegenda->ultimaLinha->commitCelula();

  $obTabelaLegenda->ultimaLinha->addCelula();
  $obTabelaLegenda->ultimaLinha->ultimaCelula->setClass( "fieldcalendario" );
  $obTabelaLegenda->ultimaLinha->ultimaCelula->addConteudo( "Feriado Fixo" );
  $obTabelaLegenda->ultimaLinha->commitCelula();

  $obTabelaLegenda->ultimaLinha->addCelula();
  $obTabelaLegenda->ultimaLinha->ultimaCelula->setClass( "feriadovariavel" );
  $obTabelaLegenda->ultimaLinha->ultimaCelula->addConteudo($stBranco);
  $obTabelaLegenda->ultimaLinha->commitCelula();

  $obTabelaLegenda->ultimaLinha->addCelula();
  $obTabelaLegenda->ultimaLinha->ultimaCelula->setClass( "fieldcalendario" );
  $obTabelaLegenda->ultimaLinha->ultimaCelula->addConteudo( "Feriado Variável" );
  $obTabelaLegenda->ultimaLinha->commitCelula();

  $obTabelaLegenda->ultimaLinha->addCelula();
  $obTabelaLegenda->ultimaLinha->ultimaCelula->setClass( "feriadopontofacultativo" );
  $obTabelaLegenda->ultimaLinha->ultimaCelula->addConteudo($stBranco);
  $obTabelaLegenda->ultimaLinha->commitCelula();

  $obTabelaLegenda->ultimaLinha->addCelula();
  $obTabelaLegenda->ultimaLinha->ultimaCelula->setClass( "fieldcalendario" );
  $obTabelaLegenda->ultimaLinha->ultimaCelula->addConteudo( "Ponto Facultativo" );
  $obTabelaLegenda->ultimaLinha->commitCelula();

  $obTabelaLegenda->ultimaLinha->addCelula();
  $obTabelaLegenda->ultimaLinha->ultimaCelula->setClass( "feriadodiacompensado" );
  $obTabelaLegenda->ultimaLinha->ultimaCelula->addConteudo($stBranco);
  $obTabelaLegenda->ultimaLinha->commitCelula();

  $obTabelaLegenda->ultimaLinha->addCelula();
  $obTabelaLegenda->ultimaLinha->ultimaCelula->setClass( "fieldcalendario" );
  $obTabelaLegenda->ultimaLinha->ultimaCelula->addConteudo( "Dia Compensado" );
  $obTabelaLegenda->ultimaLinha->commitCelula();

  $obTabelaLegenda->commitLinha();

  $obTabelaAno->addLinha();
  $obTabelaAno->ultimaLinha->addCelula();
  $obTabelaAno->ultimaLinha->ultimaCelula->setColSpan("4");
  $obTabelaAno->ultimaLinha->ultimaCelula->addTabela( $obTabelaLegenda  );
  $obTabelaAno->ultimaLinha->commitCelula();
  $obTabelaAno->commitLinha();

  $obTabelaAno->montaHtml();
  $this->setHtml( $obTabelaAno->getHtml() );
}

/**
    * Monta o HTML do Objeto Calendario
    * @access Protected
*/
function montaHtml()
{
    $stHtml = $this->getHtml();
    $this->setHtml( $stHtml );
}

}

?>
