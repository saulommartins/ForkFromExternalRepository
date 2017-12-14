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
    * Componente para informar os atributos de entrada de um ítem
    * Data de Criação: 01/04/2008

    * @author Andre Almeida

    * Casos de uso: uc-03.03.16

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$pgOc = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaAtributosEntrada.php?'.Sessao::getId();

$obIMontaAtributosEntrada = Sessao::read('IMontaAtributosEntrada');

function montaHTMLListaAtributos($boEscapeChars = true)
{
    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

    $pgOc = CAM_GP_ALM_PROCESSAMENTO.'OCIMontaAtributosEntrada.php?'.Sessao::getId();

    $obIMontaAtributosEntrada = Sessao::read('IMontaAtributosEntradaValores');

    $rsItens = new RecordSet;
    $rsItens->preenche( $obIMontaAtributosEntrada );
    $table = new Table();
    $table->setRecordset( $rsItens );
    $table->setSummary('Lista de Atributos de Entrada');

    $table->Head->addCabecalho( 'Atributos'  , 20 );
    $table->Head->addCabecalho( 'Quantidade'  , 20 );
    $table->Body->addCampo( '[stValoresAtributos]', 'C' );
    $table->Body->addCampo( '[nuQuantidadeAtributo]', 'E' );
    $table->Body->addAcao( 'alterar' ,  "ajaxJavaScript('".$pgOc."&inId='+%s,'montaAlterarItem');" , array( 'inId') );
    $table->Body->addAcao( 'excluir' ,  "ajaxJavaScript('".$pgOc."&inId='+%s,'excluirItem');" , array( 'inId') );

    $table->montaHTML($boEscapeChars);
    $stHTML = $table->getHtml();

    return $stHTML;
 }

function montaListaAtributos()
{
    $stJs  = "document.getElementById('spnListaAtributos').innerHTML = '".montaHTMLListaAtributos()."';";

    return $stJs;
}

function concatenaAtributos(&$nomeAtributos, &$valorAtributos, &$valoresAtributosGrupo, &$valoresAtributosInternos)
{
    //Concatena os nomes dos atributos e valores que são enviador por GET
    if ($stMensagem == "") {
        foreach ($_GET['stAtributo'] as $atributo => $valor) {
            if (!is_array($valor) ) {
                if ($valor != "") {
                  $nomeAtributos .= " - ".$atributo;
                  $valorAtributos .= " - ".$valor;
                  $valoresAtributosGrupo[$atributo] = " - ".$valor;
                  $valoresAtributosInternos .= " - ".$valor;
                }
            }
            if (is_array($valor) ) {
                foreach ($valor as $key => $value) {
                      if ($value['valor'] !="") {
                          $nomeAtributos .= " - ".$atributo;
                          $valorAtributos .= " - ".$value['texto'];
                          $valoresAtributosGrupo[$atributo] .= " , ".$value['valor'];
                          $valoresAtributosInternos .= " - ".$value['valor'];
                      }
                }
            }
            $valoresAtributosGrupo[$atributo] = substr( $valoresAtributosGrupo[$atributo], 3 );
        }
        $nomeAtributos = substr( $nomeAtributos, 3 );
        $valorAtributos = substr( $valorAtributos, 3 );
        $valoresAtributosInternos = substr( $valoresAtributosInternos, 3 );
    }
}

function incluirAtributos()
{
    $stMensagem = "";

    concatenaAtributos($nomeAtributos, $valorAtributos, $valoresAtributosGrupo, $valoresAtributosInternos);

    $stMensagem = validaInclusaoAlteracao($valorAtributos);
    $obIMontaAtributosEntrada = Sessao::read('IMontaAtributosEntradaValores');

    //Determina o novo ID
    $inIdNovo = 0;
    if ($stMensagem == "") {
        foreach ($obIMontaAtributosEntrada as $arAtributosValor) {
            $inIdNovo = (int) $arAtributosValor['inId'] + 1;
        }
    }

    //Inclui o item na sessao e remonta a lista
    if ($stMensagem == "") {
        $arAtributosValor = array();
        $arAtributosValor['inId'] = $inIdNovo;
        $arAtributosValor['stAtributos'] = $nomeAtributos;
        $arAtributosValor['stValoresAtributos'] = $valorAtributos;
        $arAtributosValor['nuQuantidadeAtributo'] = $_GET['nuQuantidadeAtributo'];
        $arAtributosValor['stValoresGrupo'] = $valoresAtributosGrupo;
        $arAtributosValor['stValorPadraoAtributo'] = $valoresAtributosInternos;

        $obIMontaAtributosEntrada = Sessao::read('IMontaAtributosEntradaValores');
        $obIMontaAtributosEntrada[] = $arAtributosValor;
        Sessao::write('IMontaAtributosEntradaValores', $obIMontaAtributosEntrada);

        $stJs = montaListaAtributos();
    }
    //Limpa os campos dos atributos
    if ($stMensagem == "") {
        $stJs .= limparCamposAtributo($_GET['stAtributo']);
    }

    $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";

    return $stJs;
}

function limparCamposAtributo($atributos)
{
    $stJs .= "document.getElementById('inId').value = '';";
    foreach ($atributos as $campo => $valor) {
        if (is_array($valor)) {
            $stJs .= limpaListaMultipla('stAtributo_'.$campo);
        } else {
            $stJs .= "document.getElementById('stAtributo_$campo').value = '';";
       }
    }
    $stJs .= "document.getElementById('nuQuantidadeAtributo').value = '';";
    $stJs .= "document.getElementById('btAlterarAtributos').disabled = true;";
    $stJs .= "document.getElementById('btIncluirAtributos').disabled = false;";

    return $stJs;
}

function limpaListaMultipla($lista)
{
    $disponiveis = str_replace('Selecionados','Disponiveis',$lista);
    $Selecionados = $lista;

    $Js.= "if (document.getElementById('".$lista."').type == 'select-multiple') {    ";
    $Js.= "   var i = 0 ;                                                           ";
    $Js.= "   var text = '' ;                                                       ";
    $Js.= "   var valor = '' ;                                                      ";
    $Js.= "   var idDisponiveis = '".$disponiveis."';                               ";
    $Js.= "   var idSelecionados = '".$Selecionados."';                             ";
    $Js.= "   var objSelectDisponiveis = d.getElementById(idDisponiveis);           ";
    $Js.= "   var objSelectSelecionados = d.getElementById(idSelecionados);         ";

    $Js.= "   if (objSelectSelecionados.length > 0) {                              ";
    $Js.= "      for (i = 0; i < objSelectSelecionados.length; i++) {               ";
    $Js.= "         var text = objSelectSelecionados.options[i].text;               ";
    $Js.= "         var valor = objSelectSelecionados.options[i].value;             ";

    $Js.= "         if (text != '' && valor !='') {                                  ";
    $Js.= "            var arTemp = new Option(text,valor);                         ";
    $Js.= "            destino = objSelectDisponiveis.length;                       ";
    $Js.= "            objSelectDisponiveis.options[destino] = arTemp;              ";
    $Js.= "         }                                                               ";
    $Js.= "      }                                                                  ";
    $Js.= "   }                                                                     ";

    $Js.= "   for ( i = (objSelectSelecionados.length-1); i >=0 ; i--) {             ";
    $Js.= "      objSelectSelecionados.options[i] = null;                           ";
    $Js.= "   }                                                                     ";
    $Js.= "} else {                                                                 ";
    $Js.= "   document.getElementById('$lista').value = '';                         ";
    $Js.= "}                                                                        ";

    return $Js;
}

function excluirItem()
{
    $obIMontaAtributosEntrada = Sessao::read('IMontaAtributosEntradaValores');

    //exclui o item
    foreach ($obIMontaAtributosEntrada as $linha => $dados) {
        if ($_GET['inId'] == $dados['inId']) {
            unset($obIMontaAtributosEntrada[$linha]);
        }
    }

    //refaz o indice do array
    $obIMontaAtributosEntrada = array_values($obIMontaAtributosEntrada);
    Sessao::write('IMontaAtributosEntradaValores', $obIMontaAtributosEntrada);
    $stJs = montaListaAtributos();

    return $stJs;
}

function validaInclusaoAlteracao($valorAtributos, $inId = null)
{
    $obIMontaAtributosEntrada = Sessao::read('IMontaAtributosEntradaValores');
    $arAtributosNulos = Sessao::read('atributosNulo');
    $stMensagem = "";

    if (is_array($arAtributosNulos)) {
        foreach ($arAtributosNulos as $campo => $arValNulos) {
            foreach ($_REQUEST['stAtributo'] as $campoRequest => $valorRequest) {
                if ($campo == $campoRequest) {
                    if (!is_array($valorRequest) && empty($valorRequest)) {
                        $stMensagem .= "Campo $campo inválido! ()";

                        return $stMensagem;
                    } else {
                        if (is_array($valorRequest)) {
                            foreach ($valorRequest as $indiceArray => $valorArray) {
                                if (empty($valorArray['valor'])) {
                                    if (strstr($campo,"Selecionados")) {
                                        $stMensagem .= "Campo ".$arValNulos['nome']." inválido! ()";
                                    } else {
                                        $stMensagem .= "Campo ".$campo." inválido! ()";
                                    }

                                    return $stMensagem;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    //Verifica se o campo quantidade foi preenchido
    if ( $stMensagem == "" && ($_GET['nuQuantidadeAtributo'] == "" || $_GET['nuQuantidadeAtributo'] == "0,0000") ) {
        $stMensagem = "Campo Quantidade inválido! (".$_GET['nuQuantidadeAtributo'].")";
    }

    //Verifica se os valores já existem na sessao
    if ($stMensagem == "") {
        foreach ($obIMontaAtributosEntrada as $arAtributosValor) {
            if ($arAtributosValor['stValoresAtributos'] == $valorAtributos) {
                //Se for alterar, deve permitir a alteração do mesmo
                if ( ($inId != $arAtributosValor['inId']) || (strlen($inId) == 0) ) {
                    $stMensagem = "Já existe um item com estes atributos informados!";
                    break;
                }
            }
        }
    }

    return $stMensagem;
}

function alterarAtributos()
{
    $obIMontaAtributosEntrada = Sessao::read('IMontaAtributosEntradaValores');

    $stMensagem = "";

    concatenaAtributos($nomeAtributos, $valorAtributos, $valoresAtributosGrupo, $valoresAtributosInternos);

    $stMensagem = validaInclusaoAlteracao($valorAtributos, $_GET['inId']);

    if ($stMensagem == "") {
        foreach ($obIMontaAtributosEntrada as $linha => $dados) {
            if ($_GET['inId'] == $dados['inId']) {
                $obIMontaAtributosEntrada[$linha]['stAtributos'] = $nomeAtributos;
                $obIMontaAtributosEntrada[$linha]['stValoresAtributos'] = $valorAtributos;
                $obIMontaAtributosEntrada[$linha]['nuQuantidadeAtributo'] = $_REQUEST['nuQuantidadeAtributo'];
                $obIMontaAtributosEntrada[$linha]['stValoresGrupo'] = $valoresAtributosGrupo;
                $obIMontaAtributosEntrada[$linha]['stValorPadraoAtributo'] = $valoresAtributosInternos;

                Sessao::write('IMontaAtributosEntradaValores', $obIMontaAtributosEntrada);
                $stJs = montaListaAtributos();

                $stJs .= limparCamposAtributo($_REQUEST['stAtributo']);

                break;
            }
        }
        Sessao::write('IMontaAtributosEntradaValores', $obIMontaAtributosEntrada);
    } else {
        $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}

function montaAlterarItem()
{
    $obIMontaAtributosEntrada = Sessao::read('IMontaAtributosEntradaValores');

    foreach ($obIMontaAtributosEntrada as $linha => $dados) {
        if ($_GET['inId'] == $dados['inId']) {

            if ($dados['stAtributos']) {
                $arStAtributos = explode(" - ", $dados['stAtributos']);
            }
            $arstValoresAtributos = explode(" - ", $dados['stValorPadraoAtributo']);

            $stJs .= "document.getElementById('inId').value = '".$dados['inId']."';";

            if ($arStAtributos) {
                for ( $i=0; $i<count($arStAtributos); $i++ ) {

                    $disponiveis = str_replace('Selecionados','Disponiveis',$arStAtributos[$i]);

                    $stJs .= "if (document.getElementById('stAtributo_".$arStAtributos[$i]."').type == 'select-multiple') {";

                    $stJs.= "     var i = 0 ;                                                                             ";
                    $stJs.= "     var text = '' ;                                                                         ";
                    $stJs.= "     var valor = '' ;                                                                        ";
                    $stJs.= "     var valorSelecionado = '".$arstValoresAtributos[$i]."';                                 ";
                    $stJs.= "     var idDisponiveis = 'stAtributo_".$disponiveis."';                                      ";
                    $stJs.= "     var idSelecionados = 'stAtributo_".$arStAtributos[$i]."';                               ";

                    $stJs.= "     var objSelectDisponiveis = d.getElementById(idDisponiveis);                             ";
                    $stJs.= "     var objSelectSelecionados = d.getElementById(idSelecionados);                           ";

                    $stJs.= "     if (objSelectDisponiveis.length > 0) {                                                  ";
                    $stJs.= "         for (i = 0; i < objSelectDisponiveis.length; i++) {                   ";
                    $stJs.= "              if( objSelectDisponiveis.options[i].value == valorSelecionado)   ";
                    $stJs.= "              {                                                                ";
                    $stJs.= "                  var text = objSelectDisponiveis.options[i].text;             ";
                    $stJs.= "                  var valor = objSelectDisponiveis.options[i].value;           ";
                    $stJs.= "                  objSelectDisponiveis.options[i] = null;                      ";
                    $stJs.= "              }                                                                ";
                    $stJs.= "         }                                                                     ";

                    $stJs.= "         if (text != '' && valor !='') {                                        ";
                    $stJs.= "            var arTemp = new Option(text,valor);                               ";
                    $stJs.= "            destino = objSelectSelecionados.length;                            ";
                    $stJs.= "            objSelectSelecionados.options[destino] = arTemp;                   ";
                    $stJs.= "         }                                                                     ";
                    $stJs.= "    }                                                                     ";

                    $stJs .= "} else {                                                                  ";
                    $stJs .= "    document.getElementById('stAtributo_".$arStAtributos[$i]."').value = '".$arstValoresAtributos[$i]."'; ";
                    $stJs .= "} ";
                }
            }
            $stJs .= "document.getElementById('nuQuantidadeAtributo').value = '".$dados['nuQuantidadeAtributo']."';";
            $stJs .= "document.getElementById('btAlterarAtributos').disabled = false;";
            $stJs .= "document.getElementById('btIncluirAtributos').disabled = true;";
        }
    }

    return $stJs;
}

function atualizaQuantidadeTotal($obIMontaAtributosEntrada)
{
    $obIMontaAtributosEntrada = Sessao::read('IMontaAtributosEntradaValores');

    $nuQuantidadeTotal = 0.0;

    foreach ($obIMontaAtributosEntrada as $item) {
        $stQuantidade = str_replace( '.','',  $item['nuQuantidadeAtributo'] );
        $nuQuantidade = str_replace( ',','.',  $stQuantidade );
        $nuQuantidadeTotal += $nuQuantidade;
    }
    $stQuantidadeTotal = number_format( $nuQuantidadeTotal, 4, ',', '.' );

    $stJs .= "document.getElementById('nuQuantidade').value = '".$stQuantidadeTotal."';";

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "montaListaAtributos":
        $js = montaListaAtributos();
    break;
    case "incluirAtributos":
        $js = incluirAtributos();
        $js .= atualizaQuantidadeTotal($obIMontaAtributosEntrada);
    break;
    case "excluirItem":
        $js = excluirItem();
        $js .= atualizaQuantidadeTotal($obIMontaAtributosEntrada);
    break;
    case "montaAlterarItem":
        $js = montaAlterarItem();
    break;
    case "alterarAtributos":
        $js = alterarAtributos();
        $js .= atualizaQuantidadeTotal($obIMontaAtributosEntrada);
        $js .= limparCamposAtributo($_GET['stAtributo']);
    break;
    case "limparCamposAtributo":
        $js = limparCamposAtributo($_GET['stAtributo']);
    break;
}

if( $js )
    echo $js;
