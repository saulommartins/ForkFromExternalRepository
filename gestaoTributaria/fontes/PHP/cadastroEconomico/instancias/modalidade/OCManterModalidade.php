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
    * Pagina executada no frame oculto para retornar valores para o principal
    * Data de Criação   : 04/01/2005

    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: OCManterModalidade.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.17  2007/08/06 20:09:54  cercato
Bug#9818#

Revision 1.16  2006/11/10 17:17:25  cercato
alteração do uc_05.02.13

Revision 1.15  2006/11/08 10:34:57  fabio
alteração do uc_05.02.13

Revision 1.14  2006/09/15 14:33:18  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/GA.inc.php';

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeLancamento.class.php"                             );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeAtividade.class.php"                              );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeInscricao.class.php"                              );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php"                               );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"                                   );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpMoeda.class.php"                                      );
include_once ( CAM_GT_MON_COMPONENTES."IPopUpIndicadorEconomico.class.php"                         );

function montaListaAtividades($rsAtividades , $boRetorna = false)
{
    if ( $rsAtividades->getNumLinhas() != 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsAtividades        );
        $obLista->setTitulo                    ( "Lista de Atividades Econômicas" );
        $obLista->setMostraPaginacao           ( false                );
        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"             );
        $obLista->ultimoCabecalho->setWidth    ( 5                    );
        $obLista->commitCabecalho              (                      );
        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "Código"             );
        $obLista->ultimoCabecalho->setWidth    ( 10                   );
        $obLista->commitCabecalho              (                      );
        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "Descrição"          );
        $obLista->ultimoCabecalho->setWidth    ( 25                   );
        $obLista->commitCabecalho              (                      );

        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "Modalidade"         );
        $obLista->ultimoCabecalho->setWidth    ( 30                   );
        $obLista->commitCabecalho              (                      );

        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "Valor"              );
        $obLista->ultimoCabecalho->setWidth    ( 20                   );
        $obLista->commitCabecalho              (                      );

        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "Indexador"          );
        $obLista->ultimoCabecalho->setWidth    ( 20                   );
        $obLista->commitCabecalho              (                      );

        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"             );
        $obLista->ultimoCabecalho->setWidth    ( 5                    );
        $obLista->commitCabecalho              (                      );

        $obLista->addDado                      (                      );
        $obLista->ultimoDado->setCampo         ( "valor_composto"     );
        $obLista->ultimoDado->setAlinhamento   ( "CENTRO"             );
        $obLista->commitDado                   (                      );

        $obLista->addDado                      (                      );
        $obLista->ultimoDado->setCampo         ( "nom_atividade"      );
        $obLista->commitDado                   (                      );

        $obLista->addDado                      (                      );
        $obLista->ultimoDado->setCampo         ( "nom_modalidade"     );
        $obLista->commitDado                   (                      );

        $obLista->addDado                      (                      );
        $obLista->ultimoDado->setCampo         ( "nuValor"            );
        $obLista->commitDado                   (                      );

        $obLista->addDado                      (                      );
        $obLista->ultimoDado->setCampo         ( "stDescricaoTipo"    );
        $obLista->commitDado                   (                      );

        $obLista->addAcao                      (                      );
        $obLista->ultimaAcao->setAcao          ( "Definir"            );
        $obLista->ultimaAcao->setFuncao        ( true                 );
        //$obLista->ultimaAcao->addCampo         ( "valorComposto"      ,"valor_composto"    );
        $obLista->ultimaAcao->addCampo         ( "inCodigoAtividade"  ,"cod_atividade"     );
        //$obLista->ultimaAcao->addCampo         ( "stNomeAtividade"    ,"nom_atividade"     );
        $obLista->ultimaAcao->addCampo         ( "inCodigoModalidade" ,"cod_modalidade"    );
        //$obLista->ultimaAcao->addCampo         ( "cmbCodigoModalidade","cod_modalidade"    );
        $obLista->ultimaAcao->setLink          ( "JavaScript:carregarAtividade('carregarAtividade');");
        $obLista->commitAcao                   (                      );

        $obLista->montaHTML                    (                      );
        $stHTML =  $obLista->getHtml           (                      );
        $stHTML = str_replace                  ( "\n","",$stHTML      );
        $stHTML = str_replace                  ( "  ","",$stHTML      );
        $stHTML = str_replace                  ( "'","\\'",$stHTML    );
    } else {
        $stHTML = "&nbsp;";
    }
    //$js .= "f.inCodigoAtividade.value  = '';\n";
    //$js .= "f.stValorComposto.value    = '';\n";
    //$js .= "f.stNomeAtividade.value    = '';\n";
    //$js .= "f.inCodigoModalidade.value = '';\n";
    //$js .= "f.cmbCodigoModalidade.options[0].selected = true;\n";
    $js .= "d.getElementById('spnAtividadeInscricao').innerHTML = '".$stHTML."';\n";

    if ($boRetorna) {
        return $js;
    } else {
        sistemaLegado::executaFrameOculto($js);
    }
}

$stCtrl = $_REQUEST['stCtrl'];

$obMontaAtividade = new MontaAtividade;
$bomudarTipoCarregarAtividade = false;

switch ($_REQUEST ["stCtrl"]) {
    case "atualizaFormularioFiltro":
        //$obMontaAtividade = new MontaAtividade;
        $obMontaAtividade->setCadastroAtividade( false );
        $obFormulario = new Formulario;
        if ($_REQUEST["boVinculoModalidade"] == "atividade") {
            $obHdnInCodigoAtividade = new Hidden;
            $obHdnInCodigoAtividade->setName      ( "inCodigoAtividade" );
            $obHdnInCodigoAtividade->setValue     ( $_REQUEST["inCodigoAtividade"]  );

            $obMontaAtividade->obRCEMAtividade->recuperaVigenciaAtual( $rsVigenciaAtual );

            $obMontaAtividade->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
            $obHdnCodVigencia = new Hidden;
            $obHdnCodVigencia->setName      ( "inCodigoVigencia" );
            $obHdnCodVigencia->setValue     ( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );

            $obBscAtividade = new BuscaInner;
            $obBscAtividade->setRotulo             ( "Atividade"                     );
            $obBscAtividade->setTitle              ( "Atividade Econômica"           );
            $obBscAtividade->setId                 ( "campoInner"                    );
            $obBscAtividade->setNull               ( true                            );
            $obBscAtividade->obCampoCod->setName   ( "stValorComposto"               );
            $obBscAtividade->obCampoCod->setInteiro ( false                          );
            $stBusca  = "abrePopUp('".CAM_GT_CEM_POPUPS."atividadeeconomica/FLProcurarAtividade.php','frm','stValorComposto','campoInner',''";
            $stBusca .= " ,'".Sessao::getId()."&stCadastro=modalidade','800','550')";
            $obBscAtividade->setFuncaoBusca        ( $stBusca                         );

            $obFormulario->addHidden               ( $obHdnCodVigencia                );
            $obFormulario->addHidden               ( $obHdnInCodigoAtividade          );
            $obMontaAtividade->geraFormulario      ( $obFormulario                    );
            //$obFormulario->addComponente           ( $obBscAtividade                  );
        } if ($_REQUEST["boVinculoModalidade"] == "inscricao") {
            $obBscInscricaoEconomica = new BuscaInner;
            $obBscInscricaoEconomica->setNull            ( true                                       );
            $obBscInscricaoEconomica->setRotulo          ( "Inscrição Econômica"                       );
            $obBscInscricaoEconomica->setTitle           ( "Pessoa física ou jurídica cadastrada como inscrição econômica");
            $obBscInscricaoEconomica->setId              ( "stInscricaoEconomica"                      );
            $obBscInscricaoEconomica->obCampoCod->setName( "inInscricaoEconomica"                      );
            $obBscInscricaoEconomica->obCampoCod->setSize( strlen($stMascaraInscricao)                 );
            $obBscInscricaoEconomica->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)           );
            $obBscInscricaoEconomica->obCampoCod->setMascara ( $stMascaraInscricao                     );
            $obBscInscricaoEconomica->setFuncaoBusca("abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stInscricaoEconomica','todos','".Sessao::getId()."','800','550');");
            $obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange ( "buscaDado('retornaListaAtividade');" );
            $obFormulario->addComponente           ( $obBscInscricaoEconomica          );
        }
        $obFormulario->montaInnerHTML();
        $js = "d.getElementById('spnBusca').innerHTML = '".$obFormulario->getHTML()."';\n";
        if ($_REQUEST["boVinculoModalidade"] == "atividade") {
            //$js .= "f.stValorComposto.focus();\n";
            $js .= "f.stChaveAtividade.focus();\n";
        } elseif ($_REQUEST["boVinculoModalidade"] == "inscricao") {
            $js .= "f.inInscricaoEconomica.focus();\n";
        }
        sistemaLegado::executaFrameOculto($js);
    break;
    case "retornaListaAtividade":
        $obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
        $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
        /* verificar inscrição */
        $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->consultarInscricaoEconomica($rsVerifica);
        if ( $rsVerifica->eof() ) {
            $stJs  = "f.inInscricaoEconomica.value = ''; \n";

            $stJs .= "d.getElementById('stInscricaoEconomica').innerHTML = '&nbsp;';\n";

            $stJs .= "d.getElementById('spnAtividadeInscricao').innerHTML = '&nbsp;';\n";
            $stJs .= "f.inInscricaoEconomica.focus();\n";
            $stJs .= "alertaAviso('Inscrição Ecônomica não cadastrada.','form','erro','".Sessao::getId()."', '../');";
            SistemaLegado::executaFrameOculto($stJs);

        } else {
            $obRCEMInscricaoAtividade->addAtividade();
            if ($obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica()) {
                $obRCEMInscricaoAtividade->listarAtividadesInscricao( $rsAtividades );
                $arAtividadeSessao = array ();
                $inCount = 0;
                while ( !$rsAtividades->eof() ) {
                    $arAtividadeSessao[$inCount]["nuValor"] = $rsAtividades->getCampo( "nuvalor" );
                    $arAtividadeSessao[$inCount]["stTipoValor"] = $rsAtividades->getCampo( "sttipovalor" );
                    $arAtividadeSessao[$inCount]["inCodTipo"] = $rsAtividades->getCampo( "incodtipo" );
                    $arAtividadeSessao[$inCount]["stDescricaoTipo"] = $rsAtividades->getCampo( "stdescricaotipo" );

                    $arAtividadeSessao[$inCount]["valor_composto"      ] = $rsAtividades->getCampo("valor_composto"      );
                    $arAtividadeSessao[$inCount]["cod_atividade"       ] = $rsAtividades->getCampo("cod_atividade"       );
                    $arAtividadeSessao[$inCount]["nom_atividade"       ] = $rsAtividades->getCampo("nom_atividade"       );
                    $arAtividadeSessao[$inCount]["ocorrencia_atividade"] = $rsAtividades->getCampo("ocorrencia_atividade");
                    $arAtividadeSessao[$inCount]["cod_modalidade"      ] = $rsAtividades->getCampo("cod_modalidade"      );
                    $arAtividadeSessao[$inCount]["nom_modalidade"      ] = $rsAtividades->getCampo("nom_modalidade"      );
                    $arAtividadeSessao[$inCount]["atualizar"           ] = false;
                    $rsAtividades->proximo();
                    $inCount++;
                }

                Sessao::write( "atividades", $arAtividadeSessao );

                $rsAtividades->preenche( $arAtividadeSessao );
                $rsAtividades->setPrimeiroElemento();
                $rsAtividades->addFormatacao ('nuValor','NUMERIC_BR');
                $rsAtividades->ordena("cod_atividade");
                $stJs .= "d.getElementById('stInscricaoEconomica').innerHTML = '".preg_replace("/'/","\'",$rsVerifica->getCampo("nom_cgm"))."';\n";
                $stJs .= montaListaAtividades( $rsAtividades ,TRUE);

                SistemaLegado::executaFrameOculto($stJs);
            }
        }
    break;
    case "spanMoedaIndicador":
        if ($_REQUEST["stTipoValor"] == "moeda") {
            $obIPopUpMoeda = new IPopUpMoeda;
            $obIPopUpMoeda->geraFormulario($obFormulario = new Formulario);

            $obFormulario->montaInnerHTML();
            $stJs  = "d.getElementById('spnMoedaIndicador').innerHTML = '".$obFormulario->getHTML()."';\n";
        } elseif ($_REQUEST["stTipoValor"] == "indicador") {
            $obIPopUpIndicador = new IPopUpIndicadorEconomico;
            $obIPopUpIndicador->geraFormulario($obFormulario = new Formulario);

            $obFormulario->montaInnerHTML();
            $stJs  = "d.getElementById('spnMoedaIndicador').innerHTML = '".$obFormulario->getHTML()."';\n";
        } else {
            $stJs  = "d.getElementById('spnMoedaIndicador').innerHTML = '';\n";
        }
        echo $stJs;
        break;

    case "mudarTipoCarregarAtividade":
        $bomudarTipoCarregarAtividade = true;
        if ($_REQUEST["stTipoValor"] == "indicador") {
            $boChkPercentual = $boChkMoeda = false;
            $boChkIndicador = true;
        }else
            if ($_REQUEST["stTipoValor"] == "moeda") {
                $boChkPercentual = $boChkIndicador = false;
                $boChkMoeda = true;
            }else
                if ($_REQUEST["stTipoValor"] == "percentual") {
                    $boChkMoeda = $boChkIndicador = false;
                    $boChkPercentual = true;
                }

        $nuValor = $_REQUEST["nuValor"];
        $inCodModalidade = $_REQUEST["inCodigoModalidade"];
    case "carregarAtividade":
        if (!$bomudarTipoCarregarAtividade) {
            $arAtividadeSessao = Sessao::read( "atividades" );
            for ( $inCount = 0 ; $inCount < (count($arAtividadeSessao)) ; $inCount++) {
                if ($arAtividadeSessao[$inCount]["cod_atividade" ] == $_REQUEST["inCodigoAtividade"]) {
                    if ($arAtividadeSessao[$inCount]["stTipoValor"] == "indicador") {
                        $inCodIndicador = $arAtividadeSessao[$inCount]["inCodTipo"];
                        $stDescricaoTipo = $arAtividadeSessao[$inCount]["stDescricaoTipo"];
                        $boChkPercentual = $boChkMoeda = false;
                        $boChkIndicador = true;
                    }else
                        if ($arAtividadeSessao[$inCount]["stTipoValor"] == "moeda") {
                            $inCodMoeda = $arAtividadeSessao[$inCount]["inCodTipo"];
                            $stDescricaoTipo = $arAtividadeSessao[$inCount]["stDescricaoTipo"];
                            $boChkPercentual = $boChkIndicador = false;
                            $boChkMoeda = true;
                        } else {
                            $boChkMoeda = $boChkIndicador = false;
                            $boChkPercentual = true;
                        }

                    $nuValor = $arAtividadeSessao[$inCount]["nuValor"];
                    $inCodModalidade = $arAtividadeSessao[$inCount]["inCodigoModalidade"];
                    $stDescricaoTipo = $arAtividadeSessao[$inCount]["stDescricaoTipo"];
                    break;
                }
            }
        }

        $obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );

        $obRCEMInscricaoAtividade->addAtividade();
        $mascaraAtv = $obRCEMInscricaoAtividade->roUltimaAtividade->getMascara();
        $obRCEMInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $_REQUEST['inCodigoAtividade'] );
        $obRCEMInscricaoAtividade->consultarAtividadesInscricao( $rsAtividades );
        $mascaraAtv = preg_replace( "/[0-9]/","9",$rsAtividades->getCampo("cod_estrutural"));
        $mascaraAtv = preg_replace( "/[A-Z]/","Z",strtoupper($rsAtividades->getCampo("cod_estrutural")));
        $inTamanhoAtividade = strlen( str_replace( "." , "" , $rsAtividades->getCampo("cod_estrutural") ) );
        $arAtividade = explode( "." , $rsAtividades->getCampo("cod_estrutural") );
        $inCountAtividade = count( $arAtividade );
        for ($x = 0; $x < $inCountAtividade; $x++) {
            for ($i = 0; $i <= $x; $i++) {
                $arValorAtividade[$x] .= $arAtividade[$i];
            }
            $arValorAtividade[$x] = str_pad( $arValorAtividade[$x] , $inTamanhoAtividade , "0" );
            $obMascara = new Mascara;
            $obMascara->setMascara( $mascaraAtv );
            $obMascara->setDesmascarado( $arValorAtividade[$x] );
            $obMascara->mascaraDinamica();
            $arValorAtividade[$x] = $obMascara->getMascarado();
        }

        $obLblNomAtividade = new Label;
        $obLblNomAtividade->setRotulo( "Atividade" );
        $obLblNomAtividade->setValue ( $rsAtividades->getCampo('cod_estrutural') );

        $arLabelAtividade = array();

        $obRCEMAtividade = new RCEMAtividade;
        for ($x = 0; $x < $inCountAtividade; $x++) {
            $obRCEMAtividade->setValorComposto( $arValorAtividade[$x] );
            $obRCEMAtividade->listarAtividade( $rsValorAtividade );

            $obLblAtividade = new Label;
            $obLblAtividade->setRotulo( "Atividade" );
            $obLblAtividade->setValue ( $rsValorAtividade->getCampo('nom_atividade') );
            $arLabelAtividade[] = $obLblAtividade;
        }

        if ( $rsAtividades->getCampo('principal') == 't' ) {
            $boPrincipal = "Sim";
        } else {
            $boPrincipal = "Não";
        }

        if (!$inCodModalidade) {
            $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
            $obRCEMInscricaoAtividade->addAtividade();
            $obRCEMInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $_REQUEST['inCodigoAtividade'] );
            $obRCEMInscricaoAtividade->listarAtividadesInscricao( $rsAtividadesInscricao );

            $inCodModalidade = $rsAtividadesInscricao->getCampo('cod_modalidade');
        }

        $obRCEMModalidadeLancamento = new RCEMModalidadeLancamento;
        $obRCEMModalidadeLancamento->listarModalidade( $rsModalidade );

        $obTxtModalidade = new TextBox;
        $obTxtModalidade->setRotulo            ( "*Modalidade"                     );
        $obTxtModalidade->setName              ( "inCodigoModalidade"              );
        $obTxtModalidade->setValue             ( $inCodModalidade );
        $obTxtModalidade->setSize              ( 8                                 );
        $obTxtModalidade->setMaxLength         ( 8                                 );
        $obTxtModalidade->setNull              ( true                              );
        $obTxtModalidade->setInteiro           ( true                              );

        $obCmbModalidade = new Select;
        $obCmbModalidade->setName              ( "cmbCodigoModalidade"             );
        $obCmbModalidade->addOption            ( "", "Selecione"                   );
        $obCmbModalidade->setCampoId           ( "cod_modalidade"                  );
        $obCmbModalidade->setCampoDesc         ( "nom_modalidade"                  );
        $obCmbModalidade->preencheCombo        ( $rsModalidade                     );
        $obCmbModalidade->setValue             ( $inCodModalidade );
        $obCmbModalidade->setNull              ( true                              );
        $obCmbModalidade->setStyle             ( "width: 340px"                    );

        $obRdoTipoPercentual = new Radio;
        $obRdoTipoPercentual->setName    ( "stTipoValor"  );
        $obRdoTipoPercentual->setLabel   ( "Percentual"   );
        $obRdoTipoPercentual->setValue   ( "percentual"   );
        $obRdoTipoPercentual->setRotulo  ( "Tipo de Valor"  );
        $obRdoTipoPercentual->setTitle   ( "Informe o tipo do valor da alíquota." );
        $obRdoTipoPercentual->setNull    ( false   );
        $obRdoTipoPercentual->setChecked ( $boChkPercentual  );
        $obRdoTipoPercentual->obEvento->setOnChange( "buscaDado('mudarTipoCarregarAtividade');" );

        $obRdoTipoMoeda = new Radio;
        $obRdoTipoMoeda->setName         ( "stTipoValor"   );
        $obRdoTipoMoeda->setLabel        ( "Moeda"         );
        $obRdoTipoMoeda->setValue        ( "moeda"         );
        $obRdoTipoMoeda->setChecked      ( $boChkMoeda     );
        $obRdoTipoMoeda->obEvento->setOnChange( "buscaDado('mudarTipoCarregarAtividade');" );

        $obRdoTipoIndicador = new Radio;
        $obRdoTipoIndicador->setName     ( "stTipoValor" );
        $obRdoTipoIndicador->setLabel    ( "Indicador Econômico"  );
        $obRdoTipoIndicador->setValue    ( "indicador" );
        $obRdoTipoIndicador->setChecked  ( $boChkIndicador );
        $obRdoTipoIndicador->obEvento->setOnChange( "buscaDado('mudarTipoCarregarAtividade');" );

        $obTxtValor = new Numerico;
        $obTxtValor->setRotulo    ("Valor"                                          );
        $obTxtValor->setTitle     ("Informe o valor da alíquota para a modalidade." );
        $obTxtValor->setName      ("nuValor"                                        );
        $obTxtValor->setSize      (10                                               );
        $obTxtValor->setMaxLength (10                                               );
        $obTxtValor->setNull      (false                                            );
        $obTxtValor->setNegativo  (false                                            );
        $obTxtValor->setValue     ( $nuValor );

        $obBtnDefinir = new Button;
        $obBtnDefinir->setName                 ( "btnDefinir"           );
        $obBtnDefinir->setValue                ( "Definir"              );
        $obBtnDefinir->setTipo                 ( "button"               );
        $obBtnDefinir->obEvento->setOnClick    ( "definirModalidade();" );
        $obBtnDefinir->setDisabled             ( false                  );

        $obBtnLimparDef = new Button;
        $obBtnLimparDef->setName               ( "btnLimparDef"         );
        $obBtnLimparDef->setValue              ( "Limpar"               );
        $obBtnLimparDef->setTipo               ( "button"               );
        $obBtnLimparDef->obEvento->setOnClick  ( "limparDef();"         );
        $obBtnLimparDef->setDisabled           ( false                  );

        $botoesSpanModalidade = array ( $obBtnDefinir , $obBtnLimparDef  );

        $obFormularioAtividade = new Formulario;
        $obFormularioAtividade->addTitulo( "Atividades Econômicas" );
        $obFormularioAtividade->addComponente( $obLblNomAtividade );

        foreach ($arLabelAtividade as $obLblAtividade) {
            $obFormularioAtividade->addComponente( $obLblAtividade );
        }

        //$obFormularioAtividade->agrupaComponentes( array ( $obTxtModalidade, $obCmbModalidade ) );
        $obFormularioAtividade->addComponenteComposto( $obTxtModalidade, $obCmbModalidade );
        $obFormularioAtividade->agrupaComponentes ( array ($obRdoTipoPercentual, $obRdoTipoMoeda, $obRdoTipoIndicador) );

        $obFormularioAtividade->addComponente( $obTxtValor );
        if ($boChkMoeda) {
            $obHdnInSimboloMoeda = new Hidden;
            $obHdnInSimboloMoeda->setName      ( "inSimboloMoeda"              );

            $obFormularioAtividade->addHidden( $obHdnInSimboloMoeda );

            $obIPopUpMoeda = new IPopUpMoeda;
            $obIPopUpMoeda->obCampoCod->setValue( $inCodMoeda );
            $obIPopUpMoeda->geraFormulario( $obFormularioAtividade );
        }else
            if ($boChkIndicador) {
                $obHdnInAbreviatura = new Hidden;
                $obHdnInAbreviatura->setName      ( "inAbreviatura"              );

                $obFormularioAtividade->addHidden( $obHdnInAbreviatura );

                $obIPopUpIndicador = new IPopUpIndicadorEconomico;
                $obIPopUpIndicador->obCampoCod->setValue( $inCodIndicador );
                $obIPopUpIndicador->geraFormulario( $obFormularioAtividade );
            }

        $obFormularioAtividade->defineBarra( $botoesSpanModalidade              );
        $obFormularioAtividade->montaInnerHTML();
        $stHtml = $obFormularioAtividade->getHTML();

        $stJs = "d.getElementById('spnVisualizarAtividade').innerHTML = '".$stHtml."';";
        if ($stDescricaoTipo) {
            if ($boChkMoeda) {
                $stJs .= "d.getElementById('stNomMoeda').innerHTML = '".$stDescricaoTipo."';\n";
            }else
                if ($boChkIndicador) {
                    $stJs .= "d.getElementById('stNomIndicador').innerHTML = '".$stDescricaoTipo."';\n";
                }
        }

        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "definirModalidade":
        $obRCEMModalidadeLancamento = new RCEMModalidadeLancamento;
        $obRCEMModalidadeLancamento->listarModalidade( $rsModalidade );

        $rsModalidade->setCorrente( $_REQUEST["inCodigoModalidade"] + 1 );
        $stNomeModalidade = $rsModalidade->getCampo("nom_modalidade");
        $arAtividadeSessao = Sessao::read( "atividades" );
        for ( $inCount = 0 ; $inCount < (count($arAtividadeSessao)) ; $inCount++) {
            if ($arAtividadeSessao[$inCount]["cod_atividade" ] == $_REQUEST["inCodigoAtividade"]) {
                 if ($_REQUEST["stTipoValor"] == "moeda") {
                     include_once ( CAM_GT_MON_MAPEAMENTO."TMONMoeda.class.php" );
                     $obTMONMoeda = new TMONMoeda;
                     $stFiltro = " WHERE cod_moeda = ".$_REQUEST["inCodMoeda"]." \n";
                     $obTMONMoeda->recuperaTodos( $rsMoeda, $stFiltro, " ORDER BY cod_moeda " );
                     $stDescricao = $rsMoeda->getCampo('descricao_singular');

                     $arAtividadeSessao[$inCount]["inCodTipo"] = $_REQUEST["inCodMoeda"];
                     $arAtividadeSessao[$inCount]["stDescricaoTipo"] = $stDescricao;
                 }else
                    if ($_REQUEST["stTipoValor"] == "indicador") {
                        $arAtividadeSessao[$inCount]["inCodTipo"] = $_REQUEST["inCodIndicador"];
                        $arAtividadeSessao[$inCount]["stDescricaoTipo"] = $_REQUEST["stNomIndicador"];
                    }else
                        $arAtividadeSessao[$inCount]["stDescricaoTipo"] = $_REQUEST["stTipoValor"];

                 $arAtividadeSessao[$inCount]["stTipoValor"] = $_REQUEST["stTipoValor"];
                 $arAtividadeSessao[$inCount]["nuValor"] = $_REQUEST["nuValor"];
                 $arAtividadeSessao[$inCount]["cod_modalidade"] = $_REQUEST["inCodigoModalidade"];
                 $arAtividadeSessao[$inCount]["nom_modalidade"] = $stNomeModalidade;
                 $arAtividadeSessao[$inCount]["atualizar"     ] = true;
            }
        }

        Sessao::write( "atividades", $arAtividadeSessao );
        $rsAtividades = new RecordSet;
        $rsAtividades->preenche ( $arAtividadeSessao );
        $rsAtividades->ordena("cod_atividade");
        $stJs  = "d.getElementById('spnVisualizarAtividade').innerHTML = '&nbsp;';\n";
        $stJs .= montaListaAtividades( $rsAtividades , true);
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "validaModalidade":
        $obRCEMModalidadeAtividade = new RCEMModalidadeAtividade;
        //$obRCEMModalidadeAtividade->obRCEMAtividade->setValorComposto( $_REQUEST["stValorComposto"] );
        $obRCEMModalidadeAtividade->obRCEMAtividade->setValorComposto( $_REQUEST["stChaveAtividade"] );
        $obRCEMModalidadeAtividade->obRCEMAtividade->listarAtividade( $rsAtividade );

        $obRCEMModalidadeAtividade->obRCEMAtividade->setCodigoAtividade($rsAtividade->getCampo("cod_atividade"));
        $obRCEMModalidadeAtividade->consultarModalidade();
        if ( $obRCEMModalidadeAtividade->obRCEMModalidadeLancamento->getCodigoModalidade() ) {
            $modalidadeAtividade = $obRCEMModalidadeAtividade->obRCEMModalidadeLancamento->getCodigoModalidade();
            $js .= "f.inCodigoModalidade.value  = '$modalidadeAtividade';\n";
            $js .= "f.cmbCodigoModalidade.value = '$modalidadeAtividade';\n";
        } else {
            $js .= "f.inCodigoModalidade.value  = '';\n";
            $js .= "f.cmbCodigoModalidade.options[0].selected = true;\n";
        }
        sistemaLegado::executaFrameOculto($js);
    break;
    case "preencheProxCombo":
        //$obMontaAtividade = new MontaAtividade;
        $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("§" , $stChaveLocal );
        $obMontaAtividade->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );
        $obMontaAtividade->setValorReduzido     ( $arChaveLocal[3] );

        if ( ( $_REQUEST["inPosicao"] == $_REQUEST["inNumNiveis"] ) && ( $_REQUEST["stAcao"] == "definir" ) ) {
            $obRCEMModalidadeAtividade = new RCEMModalidadeAtividade;
            $obRCEMModalidadeAtividade->obRCEMAtividade->setCodigoAtividade($arChaveLocal[1]);
            $obRCEMModalidadeAtividade->listarModalidadeAtividade($rsModalidadeAtividade,"","timestamp desc limit 1");
            if ( $rsModalidadeAtividade->getNumLinhas() > 0 ) {
                $inCodModalidade     = $rsModalidadeAtividade->getCampo( "cod_modalidade"         );
                $dtModalidade        = $rsModalidadeAtividade->getCampo( "dt_vigencia_modalidade" );
                $nuValor             = $rsModalidadeAtividade->getCampo( "valor"                  );
                $stPercentual        = $rsModalidadeAtividade->getCampo( "percentual"             );
                $stMoedaIndicador    = $rsModalidadeAtividade->getCampo( "moeda_indicador"        );
                $inCodMoedaIndicador = $rsModalidadeAtividade->getCampo( "cod_moeda_indicador"    );
                $stJs  = "f.inCodigoModalidade.value   = '".$inCodModalidade."';     \n";
                $stJs .= "f.dtDataInicio.value         = '".$dtModalidade."';        \n";
                $stJs .= "f.cmbCodigoModalidade.value  = '".$inCodModalidade."';     \n";
                $stJs .= "f.nuValor.value              = '".$nuValor."';             \n";
                if ($stPercentual == "t") {
                    $stJs .= "f.stTipoValor[0].checked = true;                       \n";
                } elseif ($stPercentual == "f" AND $stMoedaIndicador == "Moeda") {
                    $stJs .= "f.stTipoValor[1].checked = true;                       \n";

                    $obIPopUpMoeda = new IPopUpMoeda;
                    $obIPopUpMoeda->obCampoCod->setValue($inCodMoedaIndicador);
                    $obIPopUpMoeda->geraFormulario($obFormulario = new Formulario);
                    $obFormulario->montaInnerHTML();
                    $stJs .= "d.getElementById('spnMoedaIndicador').innerHTML = '".$obFormulario->getHTML()."';\n";

                    include_once ( CAM_GT_MON_MAPEAMENTO."TMONMoeda.class.php" );
                    $obTMONMoeda = new TMONMoeda;
                    $stFiltro = " WHERE cod_moeda = ".$inCodMoedaIndicador." \n";
                    $obTMONMoeda->recuperaTodos( $rsMoeda, $stFiltro, " ORDER BY cod_moeda " );
                    $stDescricao = $rsMoeda->getCampo('descricao_singular');
                    $stJs .= "d.getElementById('stNomMoeda').innerHTML = '".$stDescricao."';\n";
                } elseif ($stPercentual == "f" AND $stMoedaIndicador == "Indicador") {
                    $stJs .= "f.stTipoValor[2].checked = true;                       \n";

                    $obIPopUpIndicador = new IPopUpIndicadorEconomico;
                    $obIPopUpIndicador->obCampoCod->setValue($inCodMoedaIndicador);
                    $obIPopUpIndicador->geraFormulario($obFormulario = new Formulario);

                    $obFormulario->montaInnerHTML();
                    $stJs .= "d.getElementById('spnMoedaIndicador').innerHTML = '".$obFormulario->getHTML()."';\n";

                    include_once ( CAM_GT_MON_MAPEAMENTO."TMONIndicadorEconomico.class.php" );
                    $obTMONIndicador = new TMONIndicadorEconomico;
                    $stFiltro = " WHERE cod_indicador = ".$inCodMoedaIndicador." \n";
                    $obTMONIndicador->recuperaTodos( $rsIndicador, $stFiltro, " ORDER BY cod_indicador " );
                    $stDescricao = $rsIndicador->getCampo('descricao');
                    $stJs .= "d.getElementById('stNomIndicador').innerHTML = '".$stDescricao."';\n";
                }
            }

            $obMontaAtividade->setRetornaJs(true);

            $stJs .= $obMontaAtividade->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );

            SistemaLegado::executaFrameOculto($stJs);

        } else {
            $obMontaAtividade->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
        }
    break;
    case "preencheCombosAtividade":
        //$obMontaAtividade = new MontaAtividade;
        $obMontaAtividade->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaAtividade->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaAtividade->setValorReduzido ( $_REQUEST["stChaveAtividade"] );
        $obMontaAtividade->preencheCombosAtividade();
    break;
}
?>
