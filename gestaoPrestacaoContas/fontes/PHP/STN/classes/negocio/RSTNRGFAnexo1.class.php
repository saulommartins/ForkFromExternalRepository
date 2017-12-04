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
    * Classe de Regra do Relatório do Anexo 1
    * Data de Criação   : 23/08/2006

    * @author Analista: Cleisson Barboz
    * @author Desenvolvedor: Rodrigo

    * @package URBEM
    * @subpackage Regra

    $Revision: 59612 $
    $Name$
    $Autor: $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.01.20
*/

/*
$Log$
Revision 1.1  2006/08/25 11:37:41  rodrigo
*** empty log message ***

Revision 1.2  2006/08/04 13:48:27  jose.eduardo
Ajustes

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO   );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Demonstrativo das despesas pessoais
    * @author Desenvolvedor: Rodrigo
*/
class RSTNRGFAnexo1 extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inBimestre;

/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio    = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setEntidade($valor) { $this->stEntidade     = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setBimestre($valor) { $this->inBimestre = $valor; }

/*
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;              }
/*
    * @access Public
    * @return String
*/
function getEntidade() { return $this->stEntidade;               }
/*
    * @access Public
    * @return Integer
*/
function getBimestre() { return $this->inBimestre;           }

/**
    * Método Construtor
    * @access Private
*/
function RSTNRGFAnexo1()
{
    $this->obRRelatorio = new RRelatorio;
}

/**
    * Método abstrato
    * @access Public
*/

function geraRecordSet(&$rsRecordSet , $stOrder = "")
{
    include_once( CAM_GPC_STN_MAPEAMENTO."FSTNRGFAnexo1.class.php");
    $obFSTNRGFAnexo1 = new FSTNRGFAnexo1;

    $obFSTNRGFAnexo1->setDado("stExercicio",$this->getExercicio()    );
    $obFSTNRGFAnexo1->setDado("stEntidade" ,$this->getEntidade()     );
    $obFSTNRGFAnexo1->setDado("stBimestre" ,$this->getBimestre()         );

    $obErro = $obFSTNRGFAnexo1->recuperaDadosRelatorioAnexo1( $rsAnexo1 );
    if ( !$obErro->ocorreu() ) {
      $obErro = $obFSTNRGFAnexo1->recuperaDadosRelatorioAnexo1Cabecalho( $rsAnexo1Cabecalho );
    }
    $arBloco1 = array();
    $arBloco1[0]["descricao"] = "Municipio de ".$rsAnexo1Cabecalho->getCampo("nom_municipio")." - PODER " .$rsAnexo1Cabecalho->getCampo("parametro");
    $arBloco1[1]["descricao" ] = $rsAnexo1Cabecalho->getCampo("esfera");
    $arBloco1[2]["descricao" ] = "RELATÓRIO DE GESTÃO FISCAL";
    $arBloco1[3]["descricao" ] = "DEMONSTRATIVO DA DESPESA COM PESSOAL";
    $arBloco1[4]["descricao" ] = "ORÇAMENTOS FISCAL E DA SEGURIDADE SOCIAL";

    $inCountS = 0;
    $inCountN = 0;

    While(!($rsAnexo1->eof())){

      if ($rsAnexo1->getCampo("codigo")<=10) {

            $arBloco2[$inCountS]["nivel"] = $rsAnexo1->getCampo("nivel");
            if ($rsAnexo1->getCampo("nivel")==1) {
                $arBloco2[$inCountS]["descricao" ] = $rsAnexo1->getCampo("descricao");
            } else {
                $arBloco2[$inCountS]["descricao" ] = "     ".$rsAnexo1->getCampo("descricao");
            }
            $arBloco2[$inCountS]["valor"] = number_format($rsAnexo1->getCampo("valor"),2,',','.');
          $inCountS++;
      } else {
           $arBloco3[$inCountN]["nivel"]     = $rsAnexo1->getCampo("nivel");
           $arBloco3[$inCountN]["descricao"] = $rsAnexo1->getCampo("descricao");
           $arBloco3[$inCountN]["valor"]     = number_format($rsAnexo1->getCampo("valor"),2,',','.');
        $inCountN++;
      }
      $rsAnexo1->proximo();
    }

    $rsBloco1  = new RecordSet;
    $rsBloco1->preenche($arBloco1);

    $rsBloco2  = new RecordSet;
    $rsBloco2->preenche($arBloco2);

    $rsBloco3  = new RecordSet;
    $rsBloco3->preenche($arBloco3);

    $rsRecordSet = array($rsBloco1,$rsBloco2,$rsBloco3);

    return $obErro;
}
}
