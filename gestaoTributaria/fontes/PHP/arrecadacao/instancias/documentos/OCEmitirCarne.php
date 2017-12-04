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
  * Página de formulário oculto
  * Data de criação : 07/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * $Id: OCEmitirCarne.php 63867 2015-10-27 17:25:14Z evandro $

  Caso de uso: uc-05.03.11

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php";
include_once CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php";
include_once CAM_GT_ARR_NEGOCIO."RARRCarne.class.php";
include_once CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONConvenio.class.php";
include_once CAM_GT_CIM_MAPEAMENTO."TCIMLocalizacao.class.php";
include_once CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";
include_once CAM_GT_CIM_COMPONENTES."IPopUpImovelIntervalo.class.php";
include_once CAM_GT_CEM_COMPONENTES."IPopUpEmpresaIntervalo.class.php";

//Definicao dos nomes de arquivos
$stPrograma = "EmitirCarne";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

$stJs = "";
$stCrtlNome="";

$obRARRGrupo = new RARRGrupo;
$inCodModulo = $obRARRGrupo->getCodModulo() ;

function listaCarne($arRecordSet, $boExecuta=true)
{
    global $obRegra;

    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de carnês" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Número do Carnê" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nome" );
        $obLista->ultimoCabecalho->setWidth( 82 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Motivo" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stNumeracao" );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stNome" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stMotivo" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluiCarne');" );
        $obLista->ultimaAcao->addCampo("1","stNumeracao");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }

    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnCarne').innerHTML = '".$stHtml."';";
    $stJs .= "f.stNumeracao.value = '';";
    $stJs .= "f.inMotivo.value = '';";
    $stJs .= "f.stMotivo.selectIndex = 0;";
    $stJs .= "f.stMotivo.value = '';";

    //if ($boExecuta==true) {
    //    SistemaLegado::executaFrameOculto($stJs);
    //} else {
        return $stJs;
    //}
}

// FUNCOES PARA MONTAR COMPONNENTES
function montaBuscaContribuinte()
{
    $obBscContribuinte = new BuscaInnerIntervalo;
    $obBscContribuinte->setRotulo           ( "Contribuinte"    );
    //$obBscContribuinte->setTitle            ( "Valor Inicial para Codigo do Contribuinte");
    $obBscContribuinte->obLabelIntervalo->setValue ( "até"          );
    $obBscContribuinte->obCampoCod->setName     ("inCodContribuinteInicial"  );
    $obBscContribuinte->obCampoCod->setValue        ( $inCodContribuinteInicio  );
    $obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor('buscaContribuinteInicio');");
    $obBscContribuinte->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteInicial','stNaoExiste','','".Sessao::getId()."','800','450');" ));
    $obBscContribuinte->obCampoCod2->setName        ("inCodContribuinteFinal"  );
    $obBscContribuinte->obCampoCod2->setValue       ( $inCodContribuinteFinal  );
    $obBscContribuinte->obCampoCod2->obEvento->setOnChange("buscaValor('buscaContribuinteFinal');");
    $obBscContribuinte->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteFinal','stNaoExiste','','".Sessao::getId()."','800','450');" ));

    return $obBscContribuinte;
}

function montaBuscaInscricaoImobiliaria()
{
    $obBscInscricaoImobiliaria = new BuscaInnerIntervalo;
    $obBscInscricaoImobiliaria->setRotulo           ( "Inscrição Imobiliária"   );
    //$obBscInscricaoImobiliaria->setTitle            ( "Intervalo de Valores para Inscrição Imobiliária");
    $obBscInscricaoImobiliaria->obLabelIntervalo->setValue ( "até"          );
    $obBscInscricaoImobiliaria->obCampoCod->setName     ("inNumInscricaoImobiliariaInicial"  );
    $obBscInscricaoImobiliaria->obCampoCod->setValue        ( $inNumInscricaoImobiliariaInicial  );
    $obBscInscricaoImobiliaria->obCampoCod->obEvento->setOnChange("buscaValor('buscaIImobiliariaInicio');");
    $obBscInscricaoImobiliaria->setFuncaoBusca      ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaInicial','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));
    $obBscInscricaoImobiliaria->obCampoCod2->setName        ( "inNumInscricaoImobiliariaFinal" );
    $obBscInscricaoImobiliaria->obCampoCod2->setValue       ( $inNumInscricaoImobiliariaFinal  );
    $obBscInscricaoImobiliaria->obCampoCod2->obEvento->setOnChange("buscaValor('buscaIImobiliariaFinal');");
    $obBscInscricaoImobiliaria->setFuncaoBusca2     ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaFinal','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));

    return $obBscInscricaoImobiliaria;
}

function montaBuscaInscricaoEconomica()
{
    $obBscInscricaoEconomica = new BuscaInnerIntervalo;
    $obBscInscricaoEconomica->setRotulo         ( "Inscrição Econômica"    );
    //$obBscInscricaoEconomica->setTitle          ( "Intervalo de Valores para Inscrição Econômica");
    $obBscInscricaoEconomica->obLabelIntervalo->setValue ( "até"            );
    $obBscInscricaoEconomica->obCampoCod->setName       ("inNumInscricaoEconomicaInicial"  );
    $obBscInscricaoEconomica->obCampoCod->setValue      ( $inNumInscricaoEconomicaInicial  );
    $obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange("buscaValor('buscaIEconomicaInicio');");
    $obBscInscricaoEconomica->setFuncaoBusca("abrePopUp(&quot;".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php&quot;,&quot;frm&quot;,&quot;inNumInscricaoEconomicaInicial&quot;,&quot;stCampo&quot;,&quot;todos&quot;,&quot;".Sessao::getId()."&quot;,&quot;800&quot;,&quot;550&quot;);");
    $obBscInscricaoEconomica->obCampoCod2->setName          ( "inNumInscricaoEconomicaFinal" );
    $obBscInscricaoEconomica->obCampoCod2->setValue         ( $inNumInscricaoEconomicaFinal  );
    $obBscInscricaoEconomica->obCampoCod2->obEvento->setOnChange("buscaValor('buscaIEconomicaFinal');");
    $obBscInscricaoEconomica->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inNumInscricaoEconomicaInicial','stCampo','todos','".Sessao::getId()."','800','550');"));

    return $obBscInscricaoEconomica;
}

function montaBuscaLocalizacao()
{
    /* consulta mascara*/
    $obRCIMNivel = new RCIMNivel;
    $obRCIMNivel->mascaraNivelVigenciaAtual($stMascara);
    $obTxtLocalizacaoInicial = new Textbox;
    $obTxtLocalizacaoInicial->setName   ( "inCodLocalizacaoInicial" );
    //$obTxtLocalizacaoInicial->setTitle  ( "Localização"             );
    $obTxtLocalizacaoInicial->setRotulo ( "Localização"             );
    $obTxtLocalizacaoInicial->setMaxLength( strlen($stMascara)      );
    $obTxtLocalizacaoInicial->setMinLength( strlen($stMascara)      );
    $obTxtLocalizacaoInicial->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");
    $obTxtLocalizacaoInicial->setValue  ( $inCodLocalizacaoInicial  );

    $obLabelIntervalo = new Label;
    $obLabelIntervalo->setValue ( "&nbsp;até&nbsp;" );

    $obTxtLocalizacaoFinal = new Textbox;
    $obTxtLocalizacaoFinal->setName     ( "inCodLocalizacaoFinal"   );
    //$obTxtLocalizacaoFinal->setTitle    ( "Localização"             );
    $obTxtLocalizacaoFinal->setRotulo   ( "Localização"             );
    $obTxtLocalizacaoFinal->setMaxLength( strlen($stMascara)        );
    $obTxtLocalizacaoFinal->setMinLength( strlen($stMascara)        );
    $obTxtLocalizacaoFinal->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");
    $obTxtLocalizacaoFinal->setValue    ( $inCodLocalizacaoFinal    );

    return array( $obTxtLocalizacaoInicial, $obLabelIntervalo, $obTxtLocalizacaoFinal);
}

function montaBuscaAtividade()
{
    // consulta mascara
    $obRCEMNivelAtividade = new RCEMNivelAtividade;
    $obRCEMNivelAtividade->geraMascara( $stMascara );
    $obLabelIntervaloAtvididade = new Label;
    $obLabelIntervaloAtvididade->setValue ( "&nbsp;até&nbsp;" );

    $obTxtAtividadeInicial = new Textbox;
    $obTxtAtividadeInicial->setName     ( "inCodAtividadeInicial"   );
    //$obTxtAtividadeInicial->setTitle    ( "Atividade"               );
    $obTxtAtividadeInicial->setRotulo   ( "Atividade"               );
    $obTxtAtividadeInicial->setValue    ( $inCodAtividadeInicial    );
    $obTxtAtividadeInicial->setMaxLength( strlen($stMascara)        );
    $obTxtAtividadeInicial->setMinLength( strlen($stMascara)        );
    $obTxtAtividadeInicial->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");

    $obTxtAtividadeFinal = new Textbox;
    $obTxtAtividadeFinal->setName   ( "inCodAtividadeFinal"     );
    //$obTxtAtividadeFinal->setTitle  ( "Atividade"               );
    $obTxtAtividadeFinal->setRotulo ( "Atividade"               );
    $obTxtAtividadeFinal->setValue  ( $inCodAtividadeFinal      );
    $obTxtAtividadeFinal->setMaxLength( strlen($stMascara)      );
    $obTxtAtividadeFinal->setMinLength( strlen($stMascara)      );
    $obTxtAtividadeFinal->obEvento->setOnKeyUp("mascaraDinamico(&quot;".$stMascara."&quot;,this,event)");

    return array( $obTxtAtividadeInicial, $obLabelIntervaloAtvididade, $obTxtAtividadeFinal);
}

function montaBuscaOrdem($stTipo)
{
    $arIPTU =   array (
                    array( "inTipoOrdem" => "inscricao_imobiliaria", "stTipoOrdem" => 'Inscrição imobiliária'),
                    array( "inTipoOrdem" => "localizacao",           "stTipoOrdem" => 'Localização'          ),
                    array( "inTipoOrdem" => "lote",                  "stTipoOrdem" => 'Lote'                 ),
                    array( "inTipoOrdem" => "logradouro",            "stTipoOrdem" => 'Logradouro'           ),
                    array( "inTipoOrdem" => "bairro",                "stTipoOrdem" => 'Bairro'               ),
                    array( "inTipoOrdem" => "cep",                   "stTipoOrdem" => 'Cep'                  )
                );
    $arISS  =   array(
                    array( "inTipoOrdem" => "inscricao_economica",   "stTipoOrdem" => 'Inscrição econômica'  ),
                    array( "inTipoOrdem" => "atividade",             "stTipoOrdem" => 'Atividade'            ),
                    array( "inTipoOrdem" => "domicilio_fiscal",      "stTipoOrdem" => 'Domicílio fiscal'     ),
                    array( "inTipoOrdem" => "modalidade_lancamento", "stTipoOrdem" => 'Modalidade de lançamento')
                );

    $arGeral = array (
                    array( "inTipoOrdem" => "inscricao_imobiliaria", "stTipoOrdem" => 'Inscrição imobiliária'),
                    array( "inTipoOrdem" => "localizacao",           "stTipoOrdem" =>  'Localização'         ),
                    array( "inTipoOrdem" => "lote",                  "stTipoOrdem" =>  'Lote'                ),
                    array( "inTipoOrdem" => "logradouro",            "stTipoOrdem" => 'Logradouro'           ),
                    array( "inTipoOrdem" => "bairro",                "stTipoOrdem" => 'Bairro'               ),
                    array( "inTipoOrdem" => "cep",                   "stTipoOrdem" => 'Cep'                  ),
                    array( "inTipoOrdem" => "inscricao_economica",   "stTipoOrdem" => 'Inscrição econômica'  ),
                    array( "inTipoOrdem" => "atividade",             "stTipoOrdem" => 'Atividade'            ),
                    array( "inTipoOrdem" => "domicilio_fiscal",      "stTipoOrdem" => 'Domicílio fiscal'     ),
                    array( "inTipoOrdem" => "modalidade_lancamento", "stTipoOrdem" => 'Modalidade de lançamento')
                );

    $rsRecordSet = new RecordSet;
    $rsRecordSetS = new RecordSet;

    if ($_REQUEST['stTipoFiltro'] == 'imobiliaria') {
        $rsRecordSet->preenche( $arIPTU );
    } elseif ($_REQUEST['stTipoFiltro'] == 'economica') {
        $rsRecordSet->preenche( $arISS  );
    } elseif ($_REQUEST['stTipoFiltro'] == 'cgm') {
        $rsRecordSet->preenche( $arGeral );
    }

    $obCmbOrdemEmissao = new SelectMultiplo();
    $obCmbOrdemEmissao->setName   ('inCodOrdemSelecionado');
    $obCmbOrdemEmissao->setRotulo ( "Ordem de Emissão" );
    $obCmbOrdemEmissao->setNull   ( false );
    //$obCmbOrdemEmissao->setTitle  ( "Ordem de Emissão" );

    // lista de atributos disponiveis
    $obCmbOrdemEmissao->SetNomeLista1 ('inCodOrdemDisponivel');
    $obCmbOrdemEmissao->setCampoId1   ('inTipoOrdem');
    $obCmbOrdemEmissao->setCampoDesc1 ('stTipoOrdem');
    $obCmbOrdemEmissao->SetRecord1    ( $rsRecordSet );

    // lista de atributos selecionados
    $obCmbOrdemEmissao->SetNomeLista2 ('inCodOrdemSelecionado');
    $obCmbOrdemEmissao->setCampoId2   ('inTipoOrdem');
    $obCmbOrdemEmissao->setCampoDesc2 ('stTipoOrdem');
    $obCmbOrdemEmissao->SetRecord2    ( $rsRecordSetS );

    return $obCmbOrdemEmissao;
}

function montaBuscaInscricaoImobiliariaIndividual()
{
    $obBscInscricaoMunicipal = new BuscaInner;
    $obBscInscricaoMunicipal->setRotulo                ( "Inscrição Imobiliária"            );
    $obBscInscricaoMunicipal->obCampoCod->setName      ( "inNumInscricaoImobiliariaInicial" );
    $obBscInscricaoMunicipal->obCampoCod->setInteiro   ( false                              );
    $obBscInscricaoMunicipal->obCampoCod->obEvento->setOnChange("buscaValor('buscaIImobiliariaInicio');");
    $obBscInscricaoMunicipal->setFuncaoBusca      ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inInscricaoImobiliaria','','todos','".Sessao::getId()."','800','550');"));

    return $obBscInscricaoMunicipal;
}

function montaBuscaInscricaoEconomicaIndividual()
{
    $obBscInscricaoEconomica = new BuscaInner;
    $obBscInscricaoEconomica->setRotulo         ( "Inscrição Econômica"    );
    //$obBscInscricaoEconomica->setTitle          ( "Intervalo de Valores para Inscrição Econômica");
    $obBscInscricaoEconomica->obCampoCod->setName       ("inNumInscricaoEconomicaInicial"  );
    $obBscInscricaoEconomica->obCampoCod->setValue      ( $inNumInscricaoEconomica  );
    $obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange("buscaValor('buscaIEconomica');");
    $obBscInscricaoEconomica->setFuncaoBusca(str_replace("'","&quot;","abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inNumInscricaoEconomicaInicial','stCampo','todos','".Sessao::getId()."','800','550');"));

    return $obBscInscricaoEconomica;
}

function montaBuscaContribuinteIndividual()
{
    $obBscContribuinteIndividual = new BuscaInner;
    $obBscContribuinteIndividual->setRotulo         ( "Contribuinte Individual"    );
  //$obBscContribuinteIndividual->setTitle          ( "Codigo do Contribuinte");
    $obBscContribuinteIndividual->setId             ( "stContribuinte" );
    $obBscContribuinteIndividual->obCampoCod->setName       ("inCodContribuinteInicial"  );
    $obBscContribuinteIndividual->obCampoCod->setValue      ( $inCodContribuinteIndividual  );
    $obBscContribuinteIndividual->obCampoCod->obEvento->setOnChange("buscaContribuinteIndividual();");
    $obBscContribuinteIndividual->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteIndividual','stContribuinte','','".Sessao::getId()."','800','450');") );
    $obBscContribuinteIndividual->setValoresBusca           ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), 'frm' );

    return $obBscContribuinteIndividual;
}

function BuscarCredito($stParam1, $stParam2)
{
    $obRegra = new RARRGrupo;

    if ($_REQUEST[$stParam1]) {
        $arDados = explode("/", $_REQUEST[$stParam1]);
        $stMascara = "";
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascara );
        $stMascara .= "/9999";

        if ( strlen($_REQUEST[$stParam1]) < strlen($stMascara) ) {
            $stJs = 'f.'.$stParam1.'.value= "";';
            $stJs .= 'f.'.$stParam1.'.focus();';
            $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Código Grupo/Ano exercício incompleto. (".$_REQUEST[$stParam1].")', 'form','erro','".Sessao::getId()."');";
        } else {
            $obRARRGrupo->setCodGrupo( $arDados[0] );
            $obRARRGrupo->setExercicio( $arDados[1] );

            $obRARRGrupo->listarGrupos( $rsListaGrupo );
            if ( $rsListaGrupo->Eof() ) {
                $stJs = 'f.'.$stParam1.'.value= "";';
                $stJs .= 'f.'.$stParam1.'.focus();';
                $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Código Grupo/Ano exercício inválido. (".$_REQUEST[$stParam1].")', 'form','erro','".Sessao::getId()."');";
            } else {
                $stJs = 'd.getElementById("'.$stParam2.'").innerHTML = "'.$rsListaGrupo->getCampo("descricao").'";';
            }
        }
    } else {
        $stJs = 'f.inCodGrupo.value= "";';
        $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function limpaTodosSpans()
{
    $stJs  = "jQuery('#spnTipoCarne').html('');";
    $stJs .= "jQuery('#spnEmissao').html('');";
    $stJs .= "jQuery('#spnAtributos').html('');";

    return $stJs;
}

/*
 * Componente com Atributos da Inscrição Imobiliária.
 */
function montaAtributoImobiliario()
{
    $obFormulario = new Formulario;
    $obFormulario->addTitulo ("Dados de Atributos");

    $obCmbOrdemAtributoLote = new SelectMultiplo();
    $obCmbOrdemAtributoLote->setName  ( 'inTipoOrdemLote' );
    $obCmbOrdemAtributoLote->setRotulo( "Ordem dos Atributos do Lote" );
    $obCmbOrdemAtributoLote->setNull  ( true );
    $obCmbOrdemAtributoLote->setTitle ( 'Ordem para emissão dos dados.' );
    $obCmbOrdemAtributoLote->setOrdenacao('selecao');

    $obRCIMConfiguracao = new RCIMConfiguracao;
    $obRCIMConfiguracao->setCodigoModulo( 12 );
    $obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
    $obRCIMConfiguracao->consultarConfiguracao();
    $obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

    $obRRegra = new RCadastroDinamico ( $obRCIMConfiguracao );
    $rsAtributo = $rsOrdemAtributoLoteS = new RecordSet;
    $obRRegra->setCodCadastro('2');
    $obRRegra->obRModulo->setCodModulo('12');
    $obRRegra->recuperaAtributos ( $rsAtributo );

    while ( !$rsAtributo->eof() ) {
        $arAtributo[$rsAtributo->getCampo( 'cod_atributo' )] = $rsAtributo->getCampo( 'nom_atributo' );
        $rsAtributo->proximo();
    }

    Sessao::write( 'atributo', $arAtributo );
    $rsAtributo->setPrimeiroElemento();

    $obCmbOrdemAtributoLote->SetNomeLista1 	('inCodOrdemLoteDisponivel');
    $obCmbOrdemAtributoLote->setCampoId1   	('cod_atributo');
    $obCmbOrdemAtributoLote->setCampoDesc1 	('nom_atributo');
    $obCmbOrdemAtributoLote->setRecord1 	( $rsAtributo );

    $obCmbOrdemAtributoLote->SetNomeLista2 	('inCodOrdemLoteSelecionados');
    $obCmbOrdemAtributoLote->setCampoId2   	('cod_atributo');
    $obCmbOrdemAtributoLote->setCampoDesc2 	('nom_atributo');
    $obCmbOrdemAtributoLote->setRecord2 	( $rsOrdemAtributoLoteS );

    $obHdnOrdemLote = new Hidden;
    $obHdnOrdemLote->setName   ( "stOrdemLote" );
    $obHdnOrdemLote->setValue  ( $stOrdemLote  );

    $obCmbOrdemAtributoImovel = new SelectMultiplo();
    $obCmbOrdemAtributoImovel->setName  ( 'inTipoOrdemAtributoImovel' );
    $obCmbOrdemAtributoImovel->setRotulo( "Ordem dos Atributos do Imovel" );
    $obCmbOrdemAtributoImovel->setNull  ( true );
    $obCmbOrdemAtributoImovel->setTitle ( 'Ordem para emissão dos dados.' );
    $obCmbOrdemAtributoImovel->setOrdenacao('selecao');

    $rsOrdemAtributoImovelS = new RecordSet;
    $obRRegra = new RCadastroDinamico ( $obRCIMConfiguracao );
    $rsAtributo = $rsAtributos = new RecordSet;
    $obRRegra->setCodCadastro('4');
    $obRRegra->obRModulo->setCodModulo('12');
    $obRRegra->recuperaAtributos ( $rsAtributo );
    while ( !$rsAtributo->eof() ) {
        $arAtributo[$rsAtributo->getCampo( 'cod_atributo' )] = $rsAtributo->getCampo( 'nom_atributo' );
        $rsAtributo->proximo();
    }

    Sessao::write( 'atributo', $arAtributo );
    $rsAtributo->setPrimeiroElemento();

    // lista de atributos disponiveis
    $obCmbOrdemAtributoImovel->SetNomeLista1('inCodOrdemImovelDisponivel');
    $obCmbOrdemAtributoImovel->setCampoId1  ('cod_atributo');
    $obCmbOrdemAtributoImovel->setCampoDesc1('nom_atributo');
    $obCmbOrdemAtributoImovel->SetRecord1   ( $rsAtributo );

    // lista de atributos selecionados
    $obCmbOrdemAtributoImovel->SetNomeLista2('inCodOrdemImovelSelecionados');
    $obCmbOrdemAtributoImovel->setCampoId2  ('cod_atributo');
    $obCmbOrdemAtributoImovel->setCampoDesc2('nom_atributo');
    $obCmbOrdemAtributoImovel->SetRecord2   ( $rsOrdemAtributoImovelS );

    $obHdnOrdemImovel = new Hidden;
    $obHdnOrdemImovel->setName   ( "stOrdemImovel" );
    $obHdnOrdemImovel->setValue  ( $stOrdemImovel  );

    $obCmbOrdemAtributoEdificacao = new SelectMultiplo();
    $obCmbOrdemAtributoEdificacao->setName  ( 'inTipoOrdemAtributoEdificacao' );
    $obCmbOrdemAtributoEdificacao->setRotulo( "Ordem dos Atributos do Edificação" );
    $obCmbOrdemAtributoEdificacao->setNull  ( true );
    $obCmbOrdemAtributoEdificacao->setTitle ( 'Ordem para emissão dos dados.' );
    $obCmbOrdemAtributoEdificacao->setOrdenacao('selecao');

    $rsOrdemAtributoEdificacaoS = new RecordSet;
    $obRRegra = new RCadastroDinamico ( $obRCIMConfiguracao );
    $rsAtributo = $rsAtributos = new RecordSet;
    $obRRegra->setCodCadastro('5');
    $obRRegra->obRModulo->setCodModulo('12');
    $obRRegra->recuperaAtributos ( $rsAtributo );
    while ( !$rsAtributo->eof() ) {
        $arAtributo[$rsAtributo->getCampo( 'cod_atributo' )] = $rsAtributo->getCampo( 'nom_atributo' );
        $rsAtributo->proximo();
    }
    $rsAtributo->setPrimeiroElemento();
    Sessao::write( 'atributo', $arAtributo );
    $obCmbOrdemAtributoEdificacao->SetNomeLista1 ('inCodOrdemEdificacaoDisponivel');
    $obCmbOrdemAtributoEdificacao->setCampoId1   ('cod_atributo');
    $obCmbOrdemAtributoEdificacao->setCampoDesc1 ('nom_atributo');
    $obCmbOrdemAtributoEdificacao->setRecord1 ( $rsAtributo );

    $obCmbOrdemAtributoEdificacao->SetNomeLista2 ('inCodOrdemEdificacaoSelecionados');
    $obCmbOrdemAtributoEdificacao->setCampoId2   ('cod_atributo');
    $obCmbOrdemAtributoEdificacao->setCampoDesc2 ('nom_atributo');
    $obCmbOrdemAtributoEdificacao->setRecord2 ( $rsOrdemAtributoEdificacaoS );

    $obHdnOrdemEdificacao = new Hidden;
    $obHdnOrdemEdificacao->setName   ( "stOrdemEdificacao" );
    $obHdnOrdemEdificacao->setValue  ( $stOrdemEdificacao  );

    $obFormulario->addComponente ( $obCmbOrdemAtributoLote );
    $obFormulario->addComponente ( $obCmbOrdemAtributoImovel );
    $obFormulario->addComponente ( $obCmbOrdemAtributoEdificacao );
    $obFormulario->addHidden     ( $obHdnOrdemLote );
    $obFormulario->addHidden     ( $obHdnOrdemImovel );
    $obFormulario->addHidden     ( $obHdnOrdemEdificacao );

    $obFormulario->montaInnerHtml();
    $stHtmlAtributo = $obFormulario->getHtml();

    return $stHtmlAtributo;
}

/*
 * Componente com Atributos da Inscrição Econômica.
 */
function montaAtributoEconomico()
{
    $obFormulario = new Formulario;
    $obFormulario->addTitulo ("Dados de Atributos");

    $rsAtrFato = $rsAtrDireito = $rsAtrAutonomo = $rsAtrElemento = new RecordSet;

    $obRRegra   = new RCadastroDinamico();
    $obRRegra->obPersistenteAtributos->recuperaTodos($rsAtrFato     , ' WHERE cod_modulo = 14 AND cod_cadastro = 1 ');
    $obRRegra->obPersistenteAtributos->recuperaTodos($rsAtrDireito  , ' WHERE cod_modulo = 14 AND cod_cadastro = 2 ');
    $obRRegra->obPersistenteAtributos->recuperaTodos($rsAtrAutonomo , ' WHERE cod_modulo = 14 AND cod_cadastro = 3 ');
    $obRRegra->obPersistenteAtributos->recuperaTodos($rsAtrElemento , ' WHERE cod_modulo = 14 AND cod_cadastro = 5 ');

    # Atributos do cadastro de Empresa de Fato
    $obCmbAtrFato = new SelectMultiplo();
    $obCmbAtrFato->setNull   (true);
    $obCmbAtrFato->setName   ('inAtrFato');
    $obCmbAtrFato->setRotulo ('Ordem dos Atributos de Empresa de Fato');
    $obCmbAtrFato->setTitle  ('Atributos da Insc. Econômica Empresa de Fato');

    $obCmbAtrFato->SetNomeLista1 ('inAtrFatoDisponivel');
    $obCmbAtrFato->setCampoId1   ('cod_atributo');
    $obCmbAtrFato->setCampoDesc1 ('nom_atributo');
    $obCmbAtrFato->setRecord1    ($rsAtrFato);

    $obCmbAtrFato->SetNomeLista2 ('inAtrFatoSelecionado');
    $obCmbAtrFato->setCampoId2   ('cod_atributo');
    $obCmbAtrFato->setCampoDesc2 ('nom_atributo');
    $obCmbAtrFato->setRecord2    (new RecordSet);

    # Atributos do cadastro de Empresa de Direito
    $obCmbAtrDireito = new SelectMultiplo();
    $obCmbAtrDireito->setNull   (true);
    $obCmbAtrDireito->setName   ('inAtrDireito');
    $obCmbAtrDireito->setRotulo ('Ordem dos Atributos de Empresa de Direito');
    $obCmbAtrDireito->setTitle  ('Atributos da Insc. Econômica Empresa de Direito');

    $obCmbAtrDireito->SetNomeLista1 ('inAtrDireitoDisponivel');
    $obCmbAtrDireito->setCampoId1   ('cod_atributo');
    $obCmbAtrDireito->setCampoDesc1 ('nom_atributo');
    $obCmbAtrDireito->setRecord1    ($rsAtrDireito);

    $obCmbAtrDireito->SetNomeLista2 ('inAtrDireitoSelecionado');
    $obCmbAtrDireito->setCampoId2   ('cod_atributo');
    $obCmbAtrDireito->setCampoDesc2 ('nom_atributo');
    $obCmbAtrDireito->setRecord2    (new RecordSet);

    # Atributos do cadastro de Autônomo
    $obCmbAtrAutonomo = new SelectMultiplo();
    $obCmbAtrAutonomo->setNull   (true);
    $obCmbAtrAutonomo->setName   ('inAtrAutonomo');
    $obCmbAtrAutonomo->setRotulo ('Ordem dos Atributos de Autônomo');
    $obCmbAtrAutonomo->setTitle  ('Atributos da Insc. Econômica Autônomo');

    $obCmbAtrAutonomo->SetNomeLista1 ('inAtrAutonomoDisponivel');
    $obCmbAtrAutonomo->setCampoId1   ('cod_atributo');
    $obCmbAtrAutonomo->setCampoDesc1 ('nom_atributo');
    $obCmbAtrAutonomo->setRecord1    ($rsAtrAutonomo);

    $obCmbAtrAutonomo->SetNomeLista2 ('inAtrAutonomoSelecionado');
    $obCmbAtrAutonomo->setCampoId2   ('cod_atributo');
    $obCmbAtrAutonomo->setCampoDesc2 ('nom_atributo');
    $obCmbAtrAutonomo->setRecord2    (new RecordSet);

    # Atributos do cadastro de Elementos
    $obCmbAtrElemento = new SelectMultiplo();
    $obCmbAtrElemento->setName   ('inAtrElemento');
    $obCmbAtrElemento->setRotulo ('Ordem dos Atributos de Elementos');
    $obCmbAtrElemento->setNull   (true);
    $obCmbAtrElemento->setTitle  ('Atributos de Elemento');

    $obCmbAtrElemento->SetNomeLista1 ('inAtrElementoDisponivel');
    $obCmbAtrElemento->setCampoId1   ('cod_atributo');
    $obCmbAtrElemento->setCampoDesc1 ('nom_atributo');
    $obCmbAtrElemento->setRecord1    ($rsAtrElemento);

    $obCmbAtrElemento->SetNomeLista2 ('inAtrElementoSelecionado');
    $obCmbAtrElemento->setCampoId2   ('cod_atributo');
    $obCmbAtrElemento->setCampoDesc2 ('nom_atributo');
    $obCmbAtrElemento->setRecord2    (new RecordSet);

    $obFormulario->addComponente ($obCmbAtrFato);
    $obFormulario->addComponente ($obCmbAtrDireito);
    $obFormulario->addComponente ($obCmbAtrAutonomo);
    $obFormulario->addComponente ($obCmbAtrElemento);

    $obFormulario->montaInnerHtml();
    $stHtmlAtributo = $obFormulario->getHtml();

    return $stHtmlAtributo;
}

switch ($_REQUEST['stCtrl']) {
    case "atualizarCarneGrafica":
        SistemaLegado::executaFrameOculto("f.submit();");
    break;

    case "buscaLocalizacaoInicio":
        if ($_REQUEST["inCodLocalizacaoInicial"]) {
            $stFiltro = " WHERE codigo_composto = '".$_REQUEST["inCodLocalizacaoInicial"]."'";
            $obTCIMLocalizacao = new TCIMLocalizacao;
            $obTCIMLocalizacao->recuperaTodos( $rsLista, $stFiltro );
            if ( $rsLista->Eof() ) {
                $stJs = 'f.inCodLocalizacaoInicial.value= "";';
                $stJs .= 'f.inCodLocalizacaoInicial.focus();';
                $stJs .= "alertaAviso('@Código Localização Inválido. (".$_REQUEST["inCodLocalizacaoInicial"].")', 'form','erro','".Sessao::getId()."');";
            }
        }
        break;

    case "buscaLocalizacaoFim":
        if ($_REQUEST["inCodLocalizacaoFinal"]) {
            $stFiltro = " WHERE codigo_composto = '".$_REQUEST["inCodLocalizacaoFinal"]."'";
            $obTCIMLocalizacao = new TCIMLocalizacao;
            $obTCIMLocalizacao->recuperaTodos( $rsLista, $stFiltro );
            if ( $rsLista->Eof() ) {
                $stJs = 'f.inCodLocalizacaoFinal.value= "";';
                $stJs .= 'f.inCodLocalizacaoFinal.focus();';
                $stJs .= "alertaAviso('@Código Localização Inválido. (".$_REQUEST["inCodLocalizacaoFinal"].")', 'form','erro','".Sessao::getId()."');";
            }
        }
        break;

    case "montaOpcoesGrafica":

        $stJs = limpaTodosSpans();

        if ($_REQUEST["emissao_carnes"] == "local") {
            $rsModelos   = new RecordSet;
            $obRARRCarne = new RARRCarne;
            $obRARRCarne->listarModeloDeCarne($rsModelos, Sessao::read('acao'));

            $obCmbModelo = new Select;
            $obCmbModelo->setRotulo     ( "*Modelo" );
            $obCmbModelo->setTitle      ( "Modelo de carne" );
            $obCmbModelo->setName       ( "cmbModelo" );
            $obCmbModelo->addOption     ( "", "Selecione" );
            $obCmbModelo->setCampoId    ( "[nom_arquivo]§[cod_modelo]" );
            $obCmbModelo->setCampoDesc  ( "nom_modelo" );
            $obCmbModelo->preencheCombo ( $rsModelos );
            $obCmbModelo->setStyle      ( "width: 100%;" );

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbModelo );
            $obFormulario->montaInnerHtml();
            $stHtml = $obFormulario->getHtml();
        } else {
            # Quando a emissão de carnê for pela gráfica
            $obRadioTipoCarneImobiliario = new Radio;
            $obRadioTipoCarneImobiliario->setId     ('vinculo');
            $obRadioTipoCarneImobiliario->setName   ('vinculo');
            $obRadioTipoCarneImobiliario->setValue  ('imobiliario');
            $obRadioTipoCarneImobiliario->setRotulo ('Vínculo');
            $obRadioTipoCarneImobiliario->setTitle  ('Informe qual o vínculo desejado');
            $obRadioTipoCarneImobiliario->setLabel  ('Cadastro Imobiliário');
            $obRadioTipoCarneImobiliario->obEvento->setOnClick("montaParametrosGET('montaFiltroCarneDesonerados');");

            $obRadioTipoCarneEconomico = new Radio;
            $obRadioTipoCarneEconomico->setId    ('vinculo');
            $obRadioTipoCarneEconomico->setName  ('vinculo');
            $obRadioTipoCarneEconomico->setValue ('economico');
            $obRadioTipoCarneEconomico->setLabel ('Cadastro Econômico');
            $obRadioTipoCarneEconomico->obEvento->setOnClick("montaParametrosGET('montaFiltroCarneEconomico');");

            $obFormulario = new Formulario;
            $obFormulario->agrupaComponentes(array($obRadioTipoCarneImobiliario, $obRadioTipoCarneEconomico));
            $obFormulario->montaInnerHtml();
            $stHtml = $obFormulario->getHtml();
        }

        $stJs .= "jQuery('#spnTipoCarne').html('".$stHtml."');";

    break;

    case "BuscaCodCredito":
        $stJs = BuscarCredito( "inCodGrupo", "stGrupo" );
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaContribuinteIndividual":
        if ($_REQUEST["inCodContribuinteIndividual"] != "" ||  !empty($_REQUEST["inCodContribuinteIndividual"] )) {
            $obRCGM = new RCGM;
            $obRCGM->setNumCGM ( $_REQUEST["inCodContribuinteIndividual"] );
            $stWhere = " numcgm = ".$obRCGM->getNumCGM();
            $null = "&nbsp;";
            $obRCGM->consultar($rsCgm, $stWhere);
            $inNumLinhas = $rsCgm->getNumLinhas();
            if ($inNumLinhas <= 0) {
                $stJs .= 'f.inCodContribuinteIndividual.value = "";';
                $stJs .= 'f.inCodContribuinteIndividual.focus();';
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$null.'";';
                $stJs .= "alertaAviso('@CGM não encontrado. (".$_REQUEST["inCodContribuinteIndividual"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCgm->getCampo("nom_cgm");
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNomCgm.'";';
            }
        }
    break;

    case "grupo":
    $arLink = explode('?',$_SERVER['REQUEST_URI']);

    print '<script type="text/javascript">
                mudaTelaPrincipal( "'.$pgFormVinculo.'?'.$arLink[1].'&stAcao='.$stAcao.'&stCtrl='.$_REQUEST['stCtrl'].'" );
           </script>';
    break;

    case "credito":
    $arLink = explode('?',$_SERVER['REQUEST_URI']);

    print '<script type="text/javascript">
                mudaTelaPrincipal( "'.$pgFormVinculo.'?'.$arLink[1].'&stAcao='.$stAcao.'&stCtrl='.$_REQUEST['stCtrl'].'" );
           </script>';
    break;

    case "montaSpnEmissao":
        $stTipoEmissao  = $_REQUEST[ "stTipoEmissao"    ];
        $stTipoFiltro   = $_REQUEST[ "stTipoFiltro"     ];

        $obFormulario = new Formulario;
        $obFormulario->addTitulo("Opções para Emissão");
        switch ($stTipoEmissao) {
            case "parcial":
                if ($stTipoFiltro == "cgm") {
                    $obFormulario->addComponente( montaBuscaContribuinte() );
                } elseif ($stTipoFiltro == "imobiliaria") {
                    $obFormulario->addComponente( montaBuscaInscricaoImobiliaria() );
                    $obFormulario->agrupaComponentes( montaBuscaLocalizacao() );
                } elseif ($stTipoFiltro == "economica") {
                    //$obFormulario->agrupaComponentes( montaBuscaLocalizacao() );
                    $obFormulario->addComponente( montaBuscaInscricaoEconomica() );
                    $obFormulario->agrupaComponentes( montaBuscaAtividade() );
                }
            break;
            case "individual":
                if ($stTipoFiltro == "cgm") {
                    $obFormulario->addComponente( montaBuscaContribuinteIndividual() );
                } elseif ($stTipoFiltro == "imobiliaria") {
                    $obFormulario->addComponente( montaBuscaContribuinteIndividual() );
                    $obFormulario->addComponente( montaBuscaInscricaoImobiliariaIndividual() );
                } elseif ($stTipoFiltro == "economica") {
                    $obFormulario->addComponente( montaBuscaContribuinteIndividual() );
                    $obFormulario->addComponente( montaBuscaInscricaoEconomicaIndividual() );
                }
            break;
        }
        $obFormulario->addComponente( montaBuscaOrdem($inCodModulo) );
        $obFormulario->montaInnerHtml();
        if ($obFormulario) {
            $stHtml = $obFormulario->getHtml();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace("'","&quot;",$stHtml);
            $stHtml = str_replace('"',"'",$stHtml);
        } else {
            $stHtml = "";
        }
        $stJs = 'd.getElementById("spnEmissao").innerHTML = "'.$stHtml.'"';

    break;
    case "buscaCredito":
        $stCrtlNome="buscaCredito";
        $arValores = explode('.',$_REQUEST["inCodCredito"]);
        // array [0]> cod_credito [1]> cod_especie [2]> cod_genero [3]> cod_natureza
        $obRARRGrupo->obRMONCredito->setCodCredito  ($arValores[0]);
        $obRARRGrupo->obRMONCredito->setCodEspecie  ($arValores[1]);
        $obRARRGrupo->obRMONCredito->setCodGenero   ($arValores[2]);
        $obRARRGrupo->obRMONCredito->setCodNatureza ($arValores[3]);
        // VERIFICAR PERMISSAO
        //$obRARRGrupo->obRMONCredito->consultarCreditoPermissao();
        $obRARRGrupo->obRMONCredito->consultarCredito();

        $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
        $stDescricao = $obRARRGrupo->obRMONCredito->getDescricao();

        if ( !empty($stDescricao) && $stDescricao!='') {
            $stJs .= "d.getElementById('stCredito').innerHTML = '".$stDescricao."';\n";
            if ( !empty($stAcao) && $stAcao== 'incluir') {
                $stJs .= "d.getElementById('stTipoCalculo').checked = true;\n";
            }
            $stJs .= "f.inCodCredito.focus();\n";
        } else {
            $stJs .= "f.inCodCredito.value ='';\n";
            $stJs .= "f.inCodCredito.focus();\n";
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Crédito informado nao existe. (".$_REQUEST['inCodCredito'].")','form','erro','".Sessao::getId()."');";
        }

        SistemaLegado::executaFrameOculto ($stJs);
    break;

    case "buscaGrupo":
        $obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
        $obRARRGrupo->consultarGrupo();

        $inCodGrupo     = $obRARRGrupo->getCodGrupo () ;
        $stDescricao    = $obRARRGrupo->getDescricao() ;
        $inCodModulo    = $obRARRGrupo->getCodModulo() ;
        $stExercicio    = $obRARRGrupo->getExercicio() ;
        if ( !empty($stDescricao) ) {
            $stJs .= "d.getElementById('stCredito').innerHTML = '".$stDescricao." / ".$stExercicio."';\n";
            $stJs .= "d.getElementById('spnEmissao').innerHTML = '';\n";
            if ($stAcao == "emitir") {
                $stJs .= "f.inCodModulo.value = '".$inCodModulo."';\n";
            }
            $stJs .= "f.inExercicioGrupo.value = '".$stExercicio."';\n";
            $stJs .= "d.getElementById('stTipoEmissao').checked = false;\n";
            $stJs .= "f.inCodGrupo.focus();\n";
        } else {
            $stJs .= "f.inCodGrupo.value ='';\n";
            $stJs .= "f.inCodGrupo.focus();\n";
            $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Grupo informado nao existe. (".$_REQUEST["inCodGrupo"].")','form','erro','".Sessao::getId()."');";
        }
    break;

    case "buscaConvenio":
        $obRMONConvenio = new RMONConvenio;
        $obRMONConvenio->setNumeroConvenio( $_REQUEST['inNumConvenio'] );
        $obRMONConvenio->listarConvenioBanco( $rsConvenio );
        if ( $rsConvenio->getNumLinhas() > 0 ) {
            $stJs .= "f.inCodBanco.value = ".$rsConvenio->getCampo( "cod_banco" ).";\n";
            $stJs .= "alertaAviso('','form','erro','".Sessao::getId()."');";
        } else {
            $stJs .= "f.inNumConvenio.value ='';\n";
            $stJs .= "f.inNumConvenio.focus();\n";
            $stJs .= "alertaAviso('@Convênio informado nao existe. (".$_REQUEST["inNumConvenio"].")','form','erro','".Sessao::getId()."');";
        }
    break;

    case "LimparSessao":
        Sessao::write('Carnes', array());
    break;

    case "excluiCarne":
        $inNumeracao = $_REQUEST['inIndice1'];
        $arTmp = array ();
        $inCountArray = 0;
        $arCarnes = Sessao::read( 'Carnes' );
        $nregistros = count ( $arCarnes );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ( ( $arCarnes[$inCount]["stNumeracao"] != $inNumeracao ) ) {
                $arTmp[$inCountArray] = $arCarnes[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write( 'Carnes', $arTmp );
        $stJs = listaCarne( $arTmp );
        break;

    case "incluirCarne":
    case "montaCarne":
        $stMensagem = false;
        $arCarnes = Sessao::read( 'Carnes' );

        if ( empty($_REQUEST['stNumeracao']) ) {
            $stMensagem = 'Campo Numeração está vazio.';
        }

        if ( empty($_REQUEST['inMotivo']) ) {
            $stMensagem = 'Campo Motivo está vazio.';
        }

        if ( is_array( $arCarnes ) ) {
            foreach ($arCarnes as $campo => $valor) {
                if ($arCarnes[$campo]['stNumeracao'] === $_REQUEST['stNumeracao']) {
                    $stMensagem = " Carnê ".$_REQUEST['stNumeracao']." - já existe.";
                }
            }
        } else {
            $arCarnes = array();
        }

        if ( empty($stMensagem) ) {
            $obRARRCarne = new RARRCarne;

            $obRARRCarne->setNumeracao( $_REQUEST['stNumeracao'] );
            $obRARRCarne->setExercicio( $_REQUEST['stExercicio'] );
            $obRARRCarne->listarNomeDevolucao( $rsCarne, false );
            if ( $rsCarne->Eof() ) {
                $stJs = "alertaAviso('@Valor inválido. (Numeração:".$_REQUEST['stNumeracao']." )','form','erro','".Sessao::getId()."');";
                $stJs .= "f.stNumeracao.value = '';";
                $stJs .= "f.inMotivo.value = '';";
                $stJs .= "f.stMotivo.value = '';";
                $stJs .= "f.stNumeracao.focus();";
            } else {
                $arTmp['cod_convenio']= $rsCarne->getCampo( 'cod_convenio' );
                $arTmp['stNumeracao'] = $_REQUEST['stNumeracao'];
                $arTmp['stNome']      = $rsCarne->getCampo( 'nom_cgm' );
                $arTmp['stMotivo']    = $_REQUEST['stMotivo'];
                $arTmp['stExercicio'] = $_REQUEST['stExercicio'];
                $arTmp['inCodMotivo'] = $_REQUEST['inMotivo'];
                $arCarnes[] = $arTmp;
                Sessao::write( 'Carnes', $arCarnes );
                $stJs = listaCarne( $arCarnes );
            }
        } else {
            $stJs = "alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto ($stJs); exit;
        break;

    case "montaFiltrosNovo":
        $stTipoEmissao  = $_REQUEST["emissao_geral"];
        $obFormulario = new Formulario;
        $obFormulario->addTitulo("Opções para Emissão");
        $obFormulario->addComponente( montaBuscaContribuinte() );
        $obFormulario->addComponente( montaBuscaInscricaoImobiliaria() );
        $obFormulario->addComponente( montaBuscaInscricaoEconomica() );
        $obFormulario->montaInnerHtml();
        if ($obFormulario) {
            $stHtml = $obFormulario->getHtml();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace("'","&quot;",$stHtml);
            $stHtml = str_replace('"',"'",$stHtml);
        } else {
            $stHtml = "";
        }
        echo $stHtml;
    break;

    case "Download":
        $content_type = 'application/sxw';
        $stDocumentoCompleto = $_REQUEST["nome_arquivo_completo"];
        $stDocumento = $_REQUEST["nome_arquivo"];

        header("Content-Length: " . filesize( $stDocumentoCompleto ));
        header("Content-type: $content_type; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$stDocumento\"");
        readfile( $stDocumentoCompleto );
    break;

    case 'montaFiltroCarneDesonerados':

        $obRdbEmissaoDesoneradosNao = new Radio;
        $obRdbEmissaoDesoneradosNao->setRotulo   ( "Emissão de Carnês Desonerados" );
        $obRdbEmissaoDesoneradosNao->setName     ( "emissao_carnes_desonerados" );
        $obRdbEmissaoDesoneradosNao->setId       ( "emissao_carnes_desonerados" );
        $obRdbEmissaoDesoneradosNao->setLabel    ( "Não" );
        $obRdbEmissaoDesoneradosNao->setValue    ( "nao" );
        $obRdbEmissaoDesoneradosNao->setNull     ( false );
        $obRdbEmissaoDesoneradosNao->obEvento->setOnClick("montaParametrosGET('montaFiltroCarneImobiliario');");

        $obRdbEmissaoDesoneradosSim = new Radio;
        $obRdbEmissaoDesoneradosSim->setId       ( "emissao_carnes_desonerados" );
        $obRdbEmissaoDesoneradosSim->setName     ( "emissao_carnes_desonerados" );
        $obRdbEmissaoDesoneradosSim->setLabel    ( "Sim" );
        $obRdbEmissaoDesoneradosSim->setValue    ( "sim" );
        $obRdbEmissaoDesoneradosSim->obEvento->setOnClick("montaParametrosGET('montaFiltroCarneImobiliario');");

        $obFormulario = new Formulario;
        $obFormulario->agrupaComponentes( array($obRdbEmissaoDesoneradosNao,$obRdbEmissaoDesoneradosSim));
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHtml();

        $stJs .= "jQuery('#spnEmissaoCarneDesonerados').html('".$stHtml."');";
    break;

    case 'montaFiltroCarneImobiliario':

        $stJs  = "jQuery('#spnEmissao').html('');";
        $stJs .= "jQuery('#spnAtributos').html('');";

        $obRCIMConfiguracao = new RCIMConfiguracao;
        $obRCIMConfiguracao->setCodigoModulo( 12 );
        $obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
        $obRCIMConfiguracao->consultarConfiguracao();
        $obRCIMConfiguracao->consultarMascaraLote( $stMascaraLote );

        # Componentes para os Filtros Imobiliários.
        $obIPopUpImovel = new IPopUpImovelIntervalo;
        $obIPopUpImovel->setVerificaInscricao ( false );

        # Monta máscara de localização
        $obMontaLocalizacao = new MontaLocalizacao;
        $obMontaLocalizacao->obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigencia );
        $obMontaLocalizacao->obRCIMLocalizacao->setCodigoVigencia( $rsVigencia->getCampo( 'cod_vigencia' ));
        $obMontaLocalizacao->obRCIMLocalizacao->listarNiveis( $rsRecordSet );
        while ( !$rsRecordSet->eof() ) {
            $obMontaLocalizacao->stMascara .= $rsRecordSet->getCampo("mascara").".";
            $rsRecordSet->proximo();
        }
        $stMascaraLocalizacao = substr( $obMontaLocalizacao->getMascara(), 0 , strlen($obMontaLocalizacao->stMascara) - 1 );

        $obChkTipoInscricaoP = new Radio;
        $obChkTipoInscricaoP->setName    ("inTipoInscricao");
        $obChkTipoInscricaoP->setRotulo  ("Tipo de Inscrição Imobiliária");
        $obChkTipoInscricaoP->setLabel   ("Prediais  ");
        $obChkTipoInscricaoP->setValue   ("prediais");

        $obChkTipoInscricaoT = new Radio;
        $obChkTipoInscricaoT->setRotulo ("Tipo de Inscrição Imobiliária");
        $obChkTipoInscricaoT->setName   ("inTipoInscricao");
        $obChkTipoInscricaoT->setLabel  ("Territoriais");
        $obChkTipoInscricaoT->setValue  ("territoriais");

        $obChkTipoInscricaoTodas = new Radio;
        $obChkTipoInscricaoTodas->setRotulo ("Tipo de Inscrição Imobiliária");
        $obChkTipoInscricaoTodas->setName   ("inTipoInscricao");
        $obChkTipoInscricaoTodas->setLabel  ("Todas");
        $obChkTipoInscricaoTodas->setValue  ("");

        $obCodInicioLocalizacao = new TextBox;
        $obCodInicioLocalizacao->setName  ( "inCodInicioLocalizacao" );
        $obCodInicioLocalizacao->setId    ( "inCodInicioLocalizacao" );
        $obCodInicioLocalizacao->setRotulo( "Localização" );
        $obCodInicioLocalizacao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLocalizacao."', this, event);" );
        $obCodInicioLocalizacao->setSize( strlen($stMascaraLocalizacao)+2);
        $obCodInicioLocalizacao->setMaxLength( strlen($stMascaraLocalizacao)+2 );
        $obCodInicioLocalizacao->setTitle ( "Informe um período" ) ;

        $obLblPeriodoLocalizacao = new Label;
        $obLblPeriodoLocalizacao->setValue( " até " );

        $obCodTerminoLocalizacao = new TextBox;
        $obCodTerminoLocalizacao->setName     ( "inCodTerminoLocalizacao" );
        $obCodTerminoLocalizacao->setRotulo   ( "Localização" );
        $obCodTerminoLocalizacao->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLocalizacao."', this, event);" );
        $obCodTerminoLocalizacao->setSize( strlen($stMascaraLocalizacao)+2);
        $obCodTerminoLocalizacao->setMaxLength( strlen($stMascaraLocalizacao)+2 );
        $obCodTerminoLocalizacao->setTitle    ( "Informe um período" );

        $obCodInicioLote = new TextBox;
        $obCodInicioLote->setName  ( "inCodEnderecoInicial" );
        $obCodInicioLote->setRotulo( "Lote" );
        $obCodInicioLote->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);" );
        $obCodInicioLote->setTitle ( "Informe um período" );
        #$obCodInicioLote->setStyle     ( "width: 200px"   );

        $obLblPeriodoLote = new Label;
        $obLblPeriodoLote->setValue( " até " );

        $obCodTerminoLote = new TextBox;
        $obCodTerminoLote->setName     ( "inCodEnderecoFinal" );
        $obCodTerminoLote->obEvento->setOnKeyUp( "mascaraDinamico('".$stMascaraLote."', this, event);");
        $obCodTerminoLote->setRotulo   ( "Lote" );
        $obCodTerminoLote->setTitle    ( "Informe um período" );
        #$obCodTerminoLote->setStyle     ( "width: 200px"   );

        # Ordem de Emissão.
        $obCmbOrdemEmissao = new SelectMultiplo();
        $obCmbOrdemEmissao->setName  ( 'inTipoOrdem' );
        $obCmbOrdemEmissao->setRotulo( "*Ordem de Emissão" );
        $obCmbOrdemEmissao->setNull  ( true );
        $obCmbOrdemEmissao->setTitle ( 'Ordem para emissão dos dados.' );
        $obCmbOrdemEmissao->setOrdenacao('selecao');

        $arOrdem = array(
            array("inTipoOrdem" => "aic2.exercicio", "stTipoOrdem" => 'Exercício'),
            array("inTipoOrdem" => "aic2.inscricao", "stTipoOrdem" => 'Inscrição'),
            array("inTipoOrdem" => "aic2.cod_grupo", "stTipoOrdem" => 'Grupo de Credito')
        );

        $rsOrdem = new RecordSet;
        $rsOrdem->preenche ($arOrdem);

        $obCmbOrdemEmissao->SetNomeLista1 ('inCodOrdemDisponivel');
        $obCmbOrdemEmissao->setCampoId1   ('inTipoOrdem');
        $obCmbOrdemEmissao->setCampoDesc1 ('stTipoOrdem');
        $obCmbOrdemEmissao->setRecord1    ($rsOrdem);

        $obCmbOrdemEmissao->SetNomeLista2 ('inCodOrdemSelecionados');
        $obCmbOrdemEmissao->setCampoId2   ('inTipoOrdem');
        $obCmbOrdemEmissao->setCampoDesc2 ('stTipoOrdem');
        $obCmbOrdemEmissao->setRecord2    (new RecordSet);

        $obHdnOrdemEmissao = new Hidden;
        $obHdnOrdemEmissao->setName  ("stOrdemEmissao");
        $obHdnOrdemEmissao->setValue ($stOrdemEmissao);

        # Atributos Imobiliários.
        $stHtmlAtributo = montaAtributoImobiliario();

        $obFormulario = new Formulario;
        $obFormulario->addTitulo ("Filtros para Carnê Imobiliário");
        $obIPopUpImovel->geraFormulario  ( $obFormulario );
        $obFormulario->agrupaComponentes ( array($obChkTipoInscricaoP, $obChkTipoInscricaoT, $obChkTipoInscricaoTodas ));
        $obFormulario->agrupaComponentes ( array($obCodInicioLocalizacao, $obLblPeriodoLocalizacao ,$obCodTerminoLocalizacao) );
        $obFormulario->agrupaComponentes ( array($obCodInicioLote, $obLblPeriodoLote ,$obCodTerminoLote) );

        $obFormulario->addComponente     ( $obCmbOrdemEmissao );
        $obFormulario->addHidden	     ( $obHdnOrdemEmissao );

        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHtml();

        $stJs .= "jQuery('#spnEmissao').html('".$stHtml."');";
        $stJs .= "jQuery('#spnAtributos').html('".$stHtmlAtributo."');";

    break;

    case 'montaFiltroCarneEconomico':

        $stJs  = "jQuery('#spnEmissao').html('');";
        $stJs .= "jQuery('#spnAtributos').html('');";

        # Componente para Inscrição Econômica.
        $obIPopUPEmpresa = new IPopUpEmpresaIntervalo;
        $obIPopUPEmpresa->setVerificaInscricao ( false );

        # Ordem de Emissão.
        $obCmbOrdemEmissao = new SelectMultiplo();
        $obCmbOrdemEmissao->setName  ( 'inTipoOrdem' );
        $obCmbOrdemEmissao->setRotulo( "Ordem de Emissão" );
        $obCmbOrdemEmissao->setNull  ( false );
        $obCmbOrdemEmissao->setTitle ( 'Ordem para emissão dos dados.' );

        $arOrdem = array(
            array("inTipoOrdem" => "aic2.exercicio"          , "stTipoOrdem" => 'Exercício'),
            array("inTipoOrdem" => "cec.inscricao_economica" , "stTipoOrdem" => 'Inscrição'),
            array("inTipoOrdem" => "aic2.cod_grupo"          , "stTipoOrdem" => 'Grupo de Credito')
        );

        $rsOrdem = new RecordSet;
        $rsOrdem->preenche ($arOrdem);

        $obCmbOrdemEmissao->SetNomeLista1 ('inCodOrdemDisponivel');
        $obCmbOrdemEmissao->setCampoId1   ('inTipoOrdem');
        $obCmbOrdemEmissao->setCampoDesc1 ('stTipoOrdem');
        $obCmbOrdemEmissao->setRecord1    ($rsOrdem);

        $obCmbOrdemEmissao->SetNomeLista2 ('inCodOrdemSelecionados');
        $obCmbOrdemEmissao->setCampoId2   ('inTipoOrdem');
        $obCmbOrdemEmissao->setCampoDesc2 ('stTipoOrdem');
        $obCmbOrdemEmissao->setRecord2    (new RecordSet);

        $obHdnOrdemEmissao = new Hidden;
        $obHdnOrdemEmissao->setName  ("stOrdemEmissao");
        $obHdnOrdemEmissao->setValue ($stOrdemEmissao);

        # Formulário com os filtros para o Carnê Econômico.
        $obFormulario = new Formulario;
        $obFormulario->addTitulo ("Filtros para Carnê Econômico");
        $obIPopUPEmpresa->geraFormulario( $obFormulario );
        $obFormulario->addComponente ( $obCmbOrdemEmissao );
        $obFormulario->addHidden	 ( $obHdnOrdemEmissao );

        $obFormulario->montaInnerHtml();

        $stHtml = $obFormulario->getHtml(true);

        # Atributos Imobiliários.
        $stHtmlAtributo = montaAtributoEconomico();

        $stJs .= "jQuery('#spnEmissao').html('".$stHtml."');";
        $stJs .= "jQuery('#spnAtributos').html('".$stHtmlAtributo."');";

    break;
}

if ($stJs!=''&&$stCrtlNome!="buscaCredito") {
    echo $stJs;
}

?>
