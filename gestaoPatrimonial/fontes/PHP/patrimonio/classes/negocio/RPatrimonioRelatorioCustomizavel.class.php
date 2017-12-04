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
    * Classe de Regra de Negócio para geração de relatótio
    * Data de Criação   : 29/12/2004

    * @author Vandré Miguel Ramos

    * @ignore

    * Casos de uso: uc-03.01.09, uc-03.01.19
*/

/*
$Log$
Revision 1.18  2007/10/11 19:55:48  domluc
Ticket#10330#

Revision 1.17  2007/10/11 18:13:52  domluc
Ticket#10330#

Revision 1.16  2007/10/11 16:53:18  domluc
Ticket#10330#

Revision 1.15  2007/06/19 20:52:54  hboaventura
Bug#9422#

Revision 1.14  2007/06/18 19:59:52  hboaventura
Inclusão do campo nota fiscal

Revision 1.13  2007/05/29 18:26:13  hboaventura
Bug #8847#

Revision 1.12  2007/05/21 19:24:57  rodrigo_sr
Bug #8847#

Revision 1.11  2007/03/15 16:00:08  tonismar
bug #8733

Revision 1.10  2007/02/09 18:16:09  tonismar
bug #6946

Revision 1.9  2007/02/09 17:49:56  tonismar
bug #6946

Revision 1.8  2007/02/09 15:22:12  tonismar
bug #6946

Revision 1.7  2007/02/08 18:06:25  tonismar
bug #6946

Revision 1.6  2006/07/06 14:07:04  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/mascarasLegado.lib.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/bancoDados/postgreSQL/PersistenteRelatorio.class.php';
include_once '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/classes/mapeamento/TPatrimonioAtributoPatrimonio.class.php';

/**
    * Classe de Regra de Negócio para geração de relatótio
    * @author Desenvolvedor: Vandré Miguel Ramos
*/
class RPatrimonioRelatorioCustomizavel extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obFOrcamento;
var $stFiltro;
var $inExercicio;

/**
     * @access Public
     * @param Object $valor
*/
function setTPatrimonioAtributoPatrimonio($valor) { $this->obTPatrimonioAtributoPatrimonio  = $valor; }
function setFiltro($valor) { $this->stFiltro                         = $valor; }
function setExercicio($valor) { $this->inExercicio                      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function getTPatrimonioAtributoPatrimonio() { return $this->obTPatrimonioAtributoPatrimonio; }
function getFiltro() { return $this->stFiltro                       ; }
function getExercicio() { return $this->inExercicio                    ; }

/**
    * Método Construtor
    * @access Private
*/
function RPatrimonioRelatorioCustomizavel()
{
    $this->obTPatrimonioAtributoPatrimonio = new TPatrimonioAtributoPatrimonio;
}

/**
    * Método abstrato
    * @access Public
*/

function geraRecordSet(&$rsRecordSet , &$rsTotal, $stOrder = "")
{
    $arTotal = array();

    $inCount = 0;
    $this->obTPatrimonioAtributoPatrimonio->setDado ("stFiltro", $this->stFiltro);
    $stFiltro = Sessao::read('filtroRelatorio');
    $obErro = $this->obTPatrimonioAtributoPatrimonio->RecuperaRelatorio( $rsRecordSet,$stFiltro,$boTransacao = "");

    $inValorQuebra = 25;

    for ($icount=0;$icount <= $stFiltro[cont];$icount++) {
        if ($stFiltro[boAtributoDinamico.$icount]) {
            $inValorQuebra -= 6;
        }
    }

    if ($stFiltro[boPlaca]) {
       $inValorQuebra -= 6;
    }

    if ($stFiltro[boDataBaixa]) {
       $inValorQuebra -= 6;
    }

    if ($stFiltro[boAquisicao]) {
       $inValorQuebra -= 6;
    }
    if ($stFiltro[boEmpenho]) {
       $inValorQuebra -= 6;
    }

    if ($stFiltro[boValor]) {
       $inValorQuebra -= 6;
    }
    if ($stFiltro[boNotaFiscal]) {
       $inValorQuebra -= 6;
    }

    if ($stFiltro['codEntidade'] == 'xxx') {
        $inValorQuebra -= 10;
    }

    while ( !$rsRecordSet->eof() ) {

        //QUEBRA DE LINHA
        $stNomContaTemp = str_replace( chr(10), "", $rsRecordSet->getCampo('descricao') );
        $stNomContaTemp = wordwrap( $stNomContaTemp,30+$inValorQuebra,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
        if ( !$stFiltro['boExpandeCampos'] )
          $stNomContaTemp = smart_trunk($rsRecordSet->getCampo('descricao'));

        $arNomContaOLD = explode( chr(13), $stNomContaTemp );         //maiores ou menores

        $stNomOrgaoTemp = str_replace( chr(10), "", $rsRecordSet->getCampo('nom_orgao') );
        $stNomOrgaoTemp = wordwrap( $stNomOrgaoTemp,30+$inValorQuebra,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
        if ( !$stFiltro['boExpandeCampos'] )
          $stNomOrgaoTemp = smart_trunk($rsRecordSet->getCampo('nom_orgao'));
        $arNomOrgaoOLD = explode( chr(13), $stNomOrgaoTemp );         //maiores ou menores

        $stNomEntidadeTemp = str_replace( chr(10), "", $rsRecordSet->getCampo('entidade') );
        $stNomEntidadeTemp = wordwrap( $stNomEntidadeTemp,30+$inValorQuebra,chr(13) );    // NOTA, o valor 66 eh o q deve ser mudado pra
        if ( !$stFiltro['boExpandeCampos'] )
          $stNomEntidadeTemp = smart_trunk($rsRecordSet->getCampo('entidade'));
        $arNomEntidadeOLD = explode( chr(13), $stNomEntidadeTemp );         //maiores ou menores

        /*if ($rsRecordSet->getCorrente() == 1) {
            $inCount2 = $inCount;
            $inCount3 = $inCount;
            $inCount4 = $inCount;
        }*/

//FIM DA QUEBRA DE LINHA

        //$arTotal[$inCount]['nom_orgao'] = $rsRecordSet->getCampo('nom_orgao');//orgao

//Adicionado foreach para fazer a quebra de linha no campo selecionado

        $arTamanhos = array( count($arNomEntidadeOLD), count($arNomContaOLD), count($arNomOrgaoOLD) );
        rsort($arTamanhos);

        for ($i=0; $i<$arTamanhos[0]; $i++) {
            $arTotal[$inCount]['descricao'] = $arNomContaOLD[$i];
            $arTotal[$inCount]['nom_orgao'] = $arNomOrgaoOLD[$i];
            $arTotal[$inCount]['entidade'] = $arNomEntidadeOLD[$i];
            $inCount++;
        }

        /*if ( count($arTamanhos[0]) > 0 ) {
            for(

            foreach ($arTamanhos[0] as $stTemp) {
                    $arTotal[$inCount2]['descricao']    = $stNomContaTemp;
                    $inCount2++;
            }

        }
        if ( count($arNomOrgaoOLD) > 0 ) {

            foreach ($arNomOrgaoOLD as $stNomOrgaoTemp) {
                    $arTotal[$inCount3]['nom_orgao']    = $stNomOrgaoTemp;
                    $inCount3++;
            }

        }
        if ( count($arNomEntidadeOLD) > 0 ) {

            foreach ($arNomEntidadeOLD as $stNomEntidadeTemp) {
                    $arTotal[$inCount4]['entidade']    = $stNomEntidadeTemp;
                    $inCount4++;
            }

        }*/
//fim do foreach
        $inCount2 = $inCount-$arTamanhos[0];
        $arTotal[$inCount2]['cod_empenho'] = $rsRecordSet->getCampo('cod_empenho');
        $arTotal[$inCount2]['cod_bem'] = $rsRecordSet->getCampo('cod_bem');
        $arTotal[$inCount2]['numero_placa'] =$rsRecordSet->getCampo('numero_placa');
        $arTotal[$inCount2]['classificacao'] =$rsRecordSet->getCampo('classificacao');
        $arTotal[$inCount2]['dt_aquisicao'] =$rsRecordSet->getCampo('dt_aquisicao');
        $arTotal[$inCount2]['nota_fiscal'] =$rsRecordSet->getCampo('nota_fiscal');

        $arTotal[$inCount2]['valor_empenho'] = number_format($rsRecordSet->getCampo('valor_empenho'), 2, ',', '.');

        $flTotalGeral += $rsRecordSet->getCampo('valor_empenho');

        for ($icountdim=0;$icountdim <= $stFiltro[cont];$icountdim++) {
           if ($stFiltro[boAtributoDinamico.$icountdim]) {
              $arTotal[$inCount2]["valor_atributo".$stFiltro[boAtributoDinamico.$icountdim].""] = $rsRecordSet->getCampo("valor_atributo".$stFiltro[boAtributoDinamico.$icountdim]."");
           }
        }
        $arTotal[$inCount2]['dt_baixa'] =$rsRecordSet->getCampo('dt_baixa');
        //$inCount = $i- 1;
        $rsRecordSet->proximo();
    }

  if ($stFiltro[boValor]) {
    $arTotal[$inCount]['classificacao'] = "";

    $arTotal[$inCount+1]['classificacao'] = "TOTAL.....:";
    $arTotal[$inCount+1]['valor_empenho'] = number_format($flTotalGeral, 2, ',', '.');
  }
  $rsRecordSet = new RecordSet;
  $rsRecordSet->preenche( $arTotal );

  return $obErro;
}
}

function smart_trunk($string)
{
  $pos = strpos($string,'-');
  if ($pos) {
    return substr($string,0,$pos);
  } else {
    return $string;
  }

}
