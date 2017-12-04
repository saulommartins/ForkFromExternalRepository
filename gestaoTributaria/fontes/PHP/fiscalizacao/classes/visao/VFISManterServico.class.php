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
    * Classe de Visao do Iniciar Processo Fiscal
    * Data de Criação   : 13/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo Vaconcellos de Magalhães

    * @package URBEM
    * @subpackage Visao

*/
require_once( CAM_GT_FIS_NEGOCIO.'RFISManterServico.class.php' );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMServico.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRVencimentoParcela.class.php" );
include_once ( CAM_GT_FIS_NEGOCIO."RFISManterLevantamento.class.php" );
include_once ( CAM_GT_FIS_VISAO."VFISManterLevantamento.class.php" );

class VFISManterServico
{
private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->obMontaServico = new MontaServico;
                $this->obMontaServico->setCodigoAtividade( $request->get("inCodAtividade") );
                $this->obMontaServico->setCodigoVigenciaServico ( $request->get("inCodigoVigencia") );
    }

    public function incluirServico($param)
    {
        return $this->controller->incluir($param);
    }

    public function montaListaServicos($rsLista,$boDeducao=true)
    {
        if ( $rsLista->getNumLinhas() > 0 ) {

            $obLista = new Lista;
            $obLista->setRecordSet                 ( $rsLista );
            $obLista->setTitulo                    ( "Lista de Serviços" );
            $obLista->setTotaliza                  ( "flValorLancado,Valor Total,right,7" );

            $obLista->setMostraPaginacao           ( false );

            $obLista->addCabecalho                 (                        );
            $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
            $obLista->ultimoCabecalho->setWidth    ( 5                      );
            $obLista->commitCabecalho              (                        );

            $obLista->addCabecalho                 (                        );
            $obLista->ultimoCabecalho->addConteudo ( "Serviços"             );
            $obLista->ultimoCabecalho->setWidth    ( 20                     );
            $obLista->commitCabecalho              (                        );

            $obLista->addCabecalho                 (                        );
            $obLista->ultimoCabecalho->addConteudo ( "Aliquota (%)"         );
            $obLista->ultimoCabecalho->setWidth    ( 15                     );
            $obLista->commitCabecalho              (                        );

            $obLista->addCabecalho                 (                        );
            $obLista->ultimoCabecalho->addConteudo ( "Valor Declarado"      );
            $obLista->ultimoCabecalho->setWidth    ( 10                     );
            $obLista->commitCabecalho              (                        );

            $obLista->addCabecalho                 (                        );
            $obLista->ultimoCabecalho->addConteudo ( "Dedução incondicional"        );
            $obLista->ultimoCabecalho->setWidth    ( 10                     );
            $obLista->commitCabecalho              (                        );
            if ($boDeducao) {
            $obLista->addCabecalho                 (                        );
            $obLista->ultimoCabecalho->addConteudo ( "Dedução legal");
            $obLista->ultimoCabecalho->setWidth    ( 10                     );
            $obLista->commitCabecalho              (                        );
                        }
            $obLista->addCabecalho                 (                        );
            $obLista->ultimoCabecalho->addConteudo ( "Valor Retido"         );
            $obLista->ultimoCabecalho->setWidth    ( 10                     );
            $obLista->commitCabecalho              (                        );

            $obLista->addCabecalho                 (                        );
            $obLista->ultimoCabecalho->addConteudo ( "Valor Lançado"        );
            $obLista->ultimoCabecalho->setWidth    ( 10                     );
            $obLista->commitCabecalho              (                        );

            $obLista->addCabecalho                 (                        );
            $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
            $obLista->ultimoCabecalho->setWidth    ( 5                      );
            $obLista->commitCabecalho              (                        );

            $obLista->addDado                      (                        );
            $obLista->ultimoDado->setCampo         ( "[stServico] - [stServicoNome]"  );
            $obLista->commitDado                   (                        );

            $obLista->addDado                      (                        );
            $obLista->ultimoDado->setCampo         ( "flAliquota"  );
            $obLista->commitDado                   (                        );

            $obLista->addDado                      (                        );
            $obLista->ultimoDado->setCampo         ( "flValorDeclarado"  );
            $obLista->commitDado                   (                        );

            $obLista->addDado                      (                        );
            $obLista->ultimoDado->setCampo         ( "flDeducao"            );
            $obLista->commitDado                   (                        );
                        if ($boDeducao) {
            $obLista->addDado                      (                        );
            $obLista->ultimoDado->setCampo         ( "flDeducaoIncondicional");
            $obLista->commitDado                   (                        );
                        }
            $obLista->addDado                      (                        );
            $obLista->ultimoDado->setCampo         ( "flValorRetido"        );
            $obLista->commitDado                   (                        );

            $obLista->addDado                      (                        );
            $obLista->ultimoDado->setCampo         ( "flValorLancado"       );
            $obLista->commitDado                   (                        );

            $obLista->addAcao                      (                        );
            $obLista->ultimaAcao->setAcao          ( "ALTERAR"              );
            $obLista->ultimaAcao->setFuncao        ( true                   );
            $obLista->ultimaAcao->setLink          ( "JavaScript:alterarServico();" );
            $obLista->ultimaAcao->addCampo         ( "inIndice1", "stServico" );
            $obLista->ultimaAcao->addCampo         ( "inIndice2", "flAliquota" );
            $obLista->ultimaAcao->addCampo         ( "inIndice3", "flValorDeclarado" );
            $obLista->ultimaAcao->addCampo         ( "inIndice4", "flDeducao" );
            $obLista->ultimaAcao->addCampo         ( "inIndice5", "flDeducaoIncondicional" );
            $obLista->ultimaAcao->addCampo         ( "inIndice6", "flValorLancado" );
            $obLista->ultimaAcao->addCampo         ( "inIndice7", "flValorRetido" );

            $obLista->commitAcao                   (                        );

            $obLista->addAcao                      (                        );
            $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
            $obLista->ultimaAcao->setFuncao        ( true                   );
            $obLista->ultimaAcao->setLink          ( "JavaScript:excluirServico();" );
            $obLista->ultimaAcao->addCampo         ( "inIndice1", "stServico" );
            $obLista->ultimaAcao->addCampo         ( "inIndice2", "flAliquota" );
            $obLista->ultimaAcao->addCampo         ( "inIndice3", "flValorDeclarado" );
            $obLista->ultimaAcao->addCampo         ( "inIndice4", "flDeducao" );
            $obLista->ultimaAcao->addCampo         ( "inIndice5", "flDeducaoIncondicional" );
            $obLista->ultimaAcao->addCampo         ( "inIndice6", "flValorLancado" );
            $obLista->ultimaAcao->addCampo         ( "inIndice7", "flValorRetido" );
            $obLista->commitAcao                   (                        );

            $obLista->montaHTML                    (                        );
            $stHTML =  $obLista->getHtml           (                        );
            $stHTML = str_replace                  ( "\n","",$stHTML        );
            $stHTML = str_replace                  ( "  ","",$stHTML        );
            $stHTML = str_replace                  ( "'","\\'",$stHTML      );
            } else {
               $stHTML = "&nbsp;";
            }

            $js .= "d.getElementById('spnListaServico').innerHTML = '".$stHTML."';\n";

        return $js;

    }
    public function PreencheCGM(Request $request)
    {
         if ($request->get("inCGM")) {
            $obTCGM = new TCGM;
            $obTCGM->setDado( "numcgm", $request->get("inCGM") );
            $obTCGM->recuperaPorChave( $rsCGM );
            if ( $rsCGM->Eof() ) {
                $stJs = 'f.inCGM.value = "";';
                $stJs .= 'f.inCGM.focus();';
                $stJs .= 'd.getElementById("stCGM").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@CGM não encontrado. (".$request->get("inCGM").")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $stJs = 'd.getElementById("stCGM").innerHTML = "'.$stNomCgm.'";';
            }
        } else {
            $stJs = 'd.getElementById("stCGM").innerHTML = "&nbsp;";';
        }

        return sistemaLegado::executaFrameOculto($stJs);

    }

    public function limpaServicoLista()
    {
            $rsListaServicos = new RecordSet;
            Sessao::write( 'servicoRetencao', array() );
            Sessao::write( 'servicos_retencao_comrt', array() );
            Sessao::write( 'servicos_retencao_semrt', array() );
            $rsListaServicos->preenche ( array() );
            $stJs = $this->montaListaServicos ( $rsListaServicos );
            sistemaLegado::executaFrameOculto($stJs);

        }
    public function limpaServico(Request $request)
    {
        if ($request->get("boReterFonte")) {
            $stJs .= 'f.stChaveServico.value = "";';
            $stJs .= 'f.flValorRetido.value = "";';

            $stJs .= 'f.inCodigoUF.value = "";';
            $stJs .= 'f.inCodUF.value = "";';
            $stJs .= 'f.inCodigoMunicipio.value = "";';
            $stJs .= 'f.inCodMunicipio.value = "";';

            $stJs .= 'f.inCGM.value = "";';
            $stJs .= "d.getElementById('stCGM').innerHTML = '&nbsp;';\n";

            $inX = 0;
            while ($request->getAll()) {
                $inX++;
                $stNome = "inCodServico_".$inX;
                if ($request->get( $stNome )) {
                    if ($inX > 1) {
                        $stJs .= "limpaSelect(f.".$stNome.",1); \n";
                        $stJs .= "f.".$stNome."[0] = new Option('Selecione Sub Grupo','', 'selected');\n";
                    }

                    $stJs .= 'f.'.$stNome.'.value = "";';
                }else
                    break;
            }
        } else {
            $stJs .= 'f.stChaveServico.value = "";';
            $stJs .= 'f.flAliquota.value = "";';
            $stJs .= 'f.flValorDeclarado.value = "";';
            $stJs .= 'f.flDeducao.value = "";';

            $inX = 0;
            while ($request->getAll()) {
                $inX++;
                $stNome = "inCodServico_".$inX;
                if ($request->get( $stNome )) {
                    if ($inX > 1) {
                        $stJs .= "limpaSelect(f.".$stNome.",1); \n";
                        $stJs .= "f.".$stNome."[0] = new Option('Selecione Sub Grupo','', 'selected');\n";
                    }

                    $stJs .= 'f.'.$stNome.'.value = "";';
                }else
                    break;
            }

        }

        return sistemaLegado::executaFrameOculto( $stJs );

    }
    public function preencheMunicipio(Request $request)
    {
        $js .= "f.inCodigoMunicipio.value=''; \n";
        $js .= "limpaSelect(f.inCodMunicipio,0); \n";
        $js .= "f.inCodMunicipio[0] = new Option('Selecione','', 'selected');\n";
        if ($request->get("inCodigoUF")) {
            $obRCIMBairro = new RCIMBairro;
            $obRCIMBairro->setCodigoUF( $request->get("inCodigoUF") );
            $obRCIMBairro->listarMunicipios( $rsMunicipios );
            $inContador = 1;
            while ( !$rsMunicipios->eof() ) {
                $inCodMunicipio = $rsMunicipios->getCampo( "cod_municipio" );
                $stNomMunicipio = $rsMunicipios->getCampo( "nom_municipio" );
                $js .= "f.inCodMunicipio.options[$inContador] = new Option('".$stNomMunicipio."','".$inCodMunicipio."'); \n";
                $inContador++;
                $rsMunicipios->proximo();
            }
        }

        return sistemaLegado::executaFrameOculto($js);

    }
    public function validaData(Request $request)
    {
        if ($request->get("stCompetencia") == "") {

            $stJs = "alertaAviso('@Campo Competência deve ser preenchido antes de definir data.','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.dtEmissao.value = "";';

            return sistemaLegado::executaFrameOculto( $stJs );

        } else {

            $dtEmissao = $request->get("dtEmissao");
            $arDataEmissao = explode( "/", $dtEmissao );

            if ($arDataEmissao[1] != $request->get("stCompetencia") || $arDataEmissao[2] != $request->get("stExercicio")) {
                $stJs = "alertaAviso('@Campo Data da Emissão inválida.','form','erro','".Sessao::getId()."');";
                $stJs .= 'f.dtEmissao.value = "";';

               return sistemaLegado::executaFrameOculto( $stJs );
            }

        }

    }

    public function validaExercicio(Request $request)
    {
        $dtInicio = $request->get('inInicio');
        $dtTermino = $request->get('inTermino');
        $stExercicio = $request->get('stExercicio');
        $stCompetencia = $request->get('stCompetencia');

        $arDataInicio = explode("-",$dtInicio);
        $arDataTermino = explode("-",$dtTermino);

        if ($arDataInicio[0] > $stExercicio or $arDataTermino[0] < $stExercicio) {

            $stJs = "alertaAviso('@ Exercício fora do período de fiscalização. Período válido de ".$arDataInicio[1]."/".$arDataInicio[0]." à ".$arDataTermino[1]."/".$arDataTermino[0]."','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.stExercicio.value = "";';
            $stJs .= 'f.stExercicio.focus()';

               return sistemaLegado::executaFrameOculto( $stJs );
         }

    }

    public function alteraCompetencia(Request $request)
    {
        $dtInicio      = $request->get('inInicio');
        $dtTermino     = $request->get('inTermino');
        $stExercicio   = $request->get('stExercicio');
        $stCompetencia = $request->get('stCompetencia');

        $arDataInicio  = explode("-",$dtInicio);
        $arDataTermino = explode("-",$dtTermino);

        if ($arDataInicio[1] > $stCompetencia and
             $arDataInicio[0] >= $stExercicio or
             $arDataTermino[1] < $stCompetencia and
             $arDataTermino[0] <= $stExercicio) {

            $stJs = "alertaAviso('@ Competência fora do período de fiscalização. Pedíodo válido de ".$arDataInicio[1]."/".$arDataInicio[0]." à ".$arDataTermino[1]."/".$arDataTermino[0]."','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.stCompetencia.value = "";';
            $stJs .= 'f.stCompetencia.focus()';

            sistemaLegado::executaFrameOculto( $stJs );

            return 'false';
         }

        if ( $request->get("stExercicio") >= Sessao::getExercicio()  ) {
            $stJs = 'f.stCompetencia.value = ""; ';
            $stJs .= "alertaAviso('@Valor inválido no campo Exercicio.','form','erro','".Sessao::getId()."'); ";
            $stJs .= 'f.stExercicio.value = ""; ';
            $stJs .= 'f.stExercicio.focus(); ';
            sistemaLegado::executaFrameOculto( $stJs );

                    return 'false';
            exit;

        } elseif ( $request->get("stExercicio") == Sessao::getExercicio() ) {

            $inMes = date ("m");

            if ($request->get("stCompetencia") < $inMes) {

                $obRARRConfiguracao = new RARRConfiguracao;
                $obRARRConfiguracao->consultar();
                $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
                $arGrupoCreditoEscrituracao = preg_split( "/\//", $stCodGrupoCreditoEscrituracao );

                $obTARRVencimentoParcela = new TARRVencimentoParcela;
                $stFiltro = " WHERE cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND ano_exercicio = ".$arGrupoCreditoEscrituracao[1];
                $obTARRVencimentoParcela->recuperaTodos( $rsListaParcela, $stFiltro );
                if ( $rsListaParcela->Eof() ) {
                    $stJs = "alertaAviso('Nenhum calendário fiscal foi definido para o grupo de credito da escrituração.','form','erro','".Sessao::getId()."');";
                    sistemaLegado::executaFrameOculto( $stJs );

                            return 'false';
                    exit;
                }

                $arData = explode( "/", $rsListaParcela->getCampo("data_vencimento") );
                if ( ($request->get("stCompetencia") < $inMes-1) || (date ("d") >= $arData[0] ) )
                    $boSetaData = true;
            } else {
                $stJs = "alertaAviso('@Valor inválido no campo Competência.','form','erro','".Sessao::getId()."');";
                $stJs .= 'f.stCompetencia.value = "";';
                sistemaLegado::executaFrameOculto( $stJs );

                        return 'false';
            }
        } else {
            $boSetaData = true;
        }

        $stJs = 'f.dtEmissao.value = "";';

        sistemaLegado::executaFrameOculto($stJs);

    }
    public function LimparFormulario(Request $request)
    {
        Sessao::write( 'servicos_retencao', array() );
        Sessao::write( 'servicos_retencao_alterando', "" );
        Sessao::write( 'servicos_retencao_comrt', array() );
        Sessao::write( 'notas_retencao_comrt', array() );
        Sessao::write( 'servicos_retencao_alterando_comrt', "" );
        Sessao::write( 'servicos_retencao_semrt', array() );
        Sessao::write( 'notas_retencao_semrt', array() );
        Sessao::write( 'servicos_retencao_alterando_semrt', "" );

    }
    public function montaRetencao(Request $request)
    {
        $rsUF = new RecordSet;
        $obRCIMLogradouro = new RCIMLogradouro;
        $obRCIMLogradouro->listarUF( $rsUF );

        $rsMunicipios = new RecordSet;

        $obFormulario = new Formulario;

        if ($request->get("boReterFonte")) {
            //com retencao
            $obTxtCodUF = new TextBox;
            $obTxtCodUF->setRotulo             ( "Estado"                );
                    $obTxtCodUF->setTitle              ( "Estado onde ocorreu a retenção" );
            $obTxtCodUF->setName               ( "inCodigoUF"            );
            $obTxtCodUF->setValue              ( $inCodigoUF             );
            $obTxtCodUF->setSize               ( 8                       );
            $obTxtCodUF->setMaxLength          ( 8                       );
            $obTxtCodUF->setNull               ( false                   );
            $obTxtCodUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio')" );

            $obCmbUF = new Select;
            $obCmbUF->setName               ( "inCodUF"               );
            $obCmbUF->addOption             ( "", "Selecione"         );
            $obCmbUF->setCampoId            ( "cod_uf"                );
            $obCmbUF->setCampoDesc          ( "nom_uf"                );
            $obCmbUF->preencheCombo         ( $rsUF                   );
                    $obCmbUF->setTitle              ( "Estado onde ocorreu a retenção" );
            $obCmbUF->setValue              ( $inCodigoUF             );
            $obCmbUF->setNull               ( false                   );
            $obCmbUF->setStyle              ( "width: 220px"          );
            $obCmbUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio')" );

            $obTxtCodMunicipio = new TextBox;
            $obTxtCodMunicipio->setRotulo    ( "Munic&iacute;pio"  );
            $obTxtCodMunicipio->setName      ( "inCodigoMunicipio" );
            $obTxtCodMunicipio->setValue     ( $inCodigoMunicipio  );
                    $obTxtCodMunicipio->setTitle     ( "Municipio onde ocorreu a retenção" );

            $obTxtCodMunicipio->setSize      ( 8                   );
            $obTxtCodMunicipio->setMaxLength ( 8                   );
            $obTxtCodMunicipio->setNull      ( false               );

            $obCmbMunicipio = new Select;
            $obCmbMunicipio->setName       ( "inCodMunicipio"   );
            $obCmbMunicipio->addOption     ( "", "Selecione"    );
            $obCmbMunicipio->setCampoId    ( "cod_municipio"    );
            $obCmbMunicipio->setCampoDesc  ( "nom_municipio"    );
            $obCmbMunicipio->setValue      ( $inCodigoMunicipio );
            $obCmbMunicipio->preencheCombo ( $rsMunicipios      );
            $obCmbMunicipio->setNull       ( false              );
            $obCmbMunicipio->setStyle      ( "width: 220px"     );

            $obBscCGM = new BuscaInner;
            $obBscCGM->setRotulo         ( "CGM - Retentor" );
            $obBscCGM->setId             ( "stCGM" );
            $obBscCGM->setNull ( false );
            $obBscCGM->obCampoCod->setName       ( "inCGM" );
                    $obBscCGM->setTitle       ( "CGM do retentor do serviço" );
            $obBscCGM->obCampoCod->obEvento->setOnChange( "buscaValor('PreencheCGM');" );
            $obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stCGM','','".Sessao::getId()."','800','450');" );

            $obTxtValorRetido = new Numerico;
            $obTxtValorRetido->setRotulo ( "Valor Retido" );
            $obTxtValorRetido->setName ( "flValorRetido" );
            $obTxtValorRetido->setId ( "flValorRetido" );
            $obTxtValorRetido->setDecimais ( 2 );
                    $obTxtValorRetido->setTitle("Valor retido do serviço");
            $obTxtValorRetido->setMaxValue ( 99999999999999.99 );
            $obTxtValorRetido->setNull ( false );
            $obTxtValorRetido->setNegativo ( false );
            $obTxtValorRetido->setNaoZero ( true );
            $obTxtValorRetido->setSize ( 20 );
            $obTxtValorRetido->setMaxLength ( 20 );

            $obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF );
            $obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
            $obFormulario->addComponente ( $obBscCGM );

            //$this->obMontaServico->obTxtChaveServico->setTitle("Informe o serviço prestado");
            $this->obMontaServico->setCodigoAtividade( $request->get("inCodAtividade") );
            $this->obMontaServico->setCodigoVigenciaServico ( $request->get("inCodigoVigencia") );
            $this->obMontaServico->setRotulo("*Serviço");

            $this->obMontaServico->geraFormulario( $obFormulario );

            $obFormulario->addComponente ( $obTxtValorRetido );

        } else {
            //sem retencao

            $obTxtAliquota = new TextBox;
            $obTxtAliquota->setRotulo ( "Alíquota (%)" );
            $obTxtAliquota->setName ( "flAliquota" );
            $obTxtAliquota->setId ( "flAliquota" );
                    $obTxtAliquota->setInteiro ( true );
            $obTxtAliquota->setNull ( false );
            $obTxtAliquota->setNaoZero ( true );
            $obTxtAliquota->setSize ( 6 );
                    $obTxtAliquota->setTitle ( "Alíquota a incidir sobre o serviço" );
            $obTxtAliquota->setMaxLength ( 6 );

            $obTxtValorDeclarado = new Numerico;
            $obTxtValorDeclarado->setRotulo ( "Valor Declarado" );
            $obTxtValorDeclarado->setName ( "flValorDeclarado" );
            $obTxtValorDeclarado->setId ( "flValorDeclarado" );
                    $obTxtValorDeclarado->setTitle( "Valor declarado para o serviço" );
            $obTxtValorDeclarado->setDecimais ( 2 );
            $obTxtValorDeclarado->setMaxValue ( 99999999999999.99 );
            $obTxtValorDeclarado->setNull ( false );
            $obTxtValorDeclarado->setNegativo ( false );
            $obTxtValorDeclarado->setNaoZero ( true );
            $obTxtValorDeclarado->setSize ( 20 );
            $obTxtValorDeclarado->setMaxLength ( 20 );

            $obTxtDeducao = new Numerico;
            $obTxtDeducao->setRotulo ( "*Dedução Legal" );
            $obTxtDeducao->setTitle ( "Deduções que possam incidir sobre o serviço" );
            $obTxtDeducao->setName ( "flDeducao" );
            $obTxtDeducao->setId ( "flDeducao" );
            $obTxtDeducao->setDecimais ( 2 );
            $obTxtDeducao->setMaxValue ( 99999999999999.99 );
            $obTxtDeducao->setNull ( true );
            $obTxtDeducao->setNegativo ( false );
            $obTxtDeducao->setNaoZero ( true );
            $obTxtDeducao->setSize ( 20 );
            $obTxtDeducao->setMaxLength ( 20 );

            $obTxtDeducaoIncondicional = new Numerico;
            $obTxtDeducaoIncondicional->setRotulo ( "*Dedução Incondicional" );
            $obTxtDeducaoIncondicional->setTitle ( "Deduções incondicionais que possam incidir sobre o serviço" );
            $obTxtDeducaoIncondicional->setName ( "flDeducaoIncondicional" );
            $obTxtDeducaoIncondicional->setId ( "flDeducaoIncondicional" );
            $obTxtDeducaoIncondicional->setDecimais ( 2 );
            $obTxtDeducaoIncondicional->setMaxValue ( 99999999999999.99 );
            $obTxtDeducaoIncondicional->setNull ( true );
            $obTxtDeducaoIncondicional->setNegativo ( false );
            $obTxtDeducaoIncondicional->setNaoZero ( true );
            $obTxtDeducaoIncondicional->setSize ( 20 );
            $obTxtDeducaoIncondicional->setMaxLength ( 20 );

            $this->obMontaServico->setCodigoAtividade( $request->get("inCodAtividade") );
            $this->obMontaServico->setCodigoVigenciaServico ( $request->get("inCodigoVigencia") );
            $this->obMontaServico->setRotulo("*Serviço");
            $this->obMontaServico->geraFormulario( $obFormulario );

            $obFormulario->addComponente( $obTxtAliquota );
            $obFormulario->addComponente( $obTxtValorDeclarado );
            $obFormulario->addComponente( $obTxtDeducao );
            $obFormulario->addComponente($obTxtDeducaoIncondicional);
        }

        $obFormulario2 = new Formulario;

        Sessao::write( 'setar_data', $boSetaData );

        $obFormulario->montaInnerHTML();
        $stJs = "d.getElementById('spn1').innerHTML = '". $obFormulario->getHTML(). "';\n";

        $stJs .= "f.boReter.value = '".$request->get("boReterFonte")."';\n";

        $boTemValores = false;
        $arServicoRetencao = Sessao::read( 'servicos_retencao' );
        for ( $inContaRetencao=0; $inContaRetencao<count( $arServicoRetencao ); $inContaRetencao++ ) {
            if ($arServicoRetencao[$inContaRetencao]["flValorDeclarado"]) {
                $boTemValores = true;
                break;
            }
        }

        $rsListaServicos = new RecordSet;
        $rsListaNotas = new RecordSet;
        if ($request->get("stEscrituracao") == "nota") {
            if ($request->get("boReter")) { //com retencao
                if ( Sessao::read( 'servicos_retencao_comrt' ) )
                    $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao_comrt' ) );

                if ( Sessao::read( 'notas_retencao_comrt' ) )
                    $rsListaNotas->preenche ( Sessao::read( 'notas_retencao_comrt' ) );
            } else {
                if ( Sessao::read( 'servicos_retencao_semrt' ) )
                    $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao_semrt' ) );

                if ( Sessao::read( 'notas_retencao_semrt' ) )
                    $rsListaNotas->preenche ( Sessao::read( 'notas_retencao_semrt' ) );
            }
        } else { //por servico
            if ( Sessao::read( 'servicos_retencao' ) )
                $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao' ) );
        }
        $stJs2 = null;
        $stJs2 = $this->montaListaServicos( $rsListaServicos );

        sistemaLegado::executaFrameOculto($stJs);
        sistemaLegado::executaFrameOculto($stJs2);

        $obRCEMServico = new RCEMServico;
        $obRCEMServico->setCodigoVigencia ( $request->get("inCodigoVigencia") );

        $obRCEMServico->recuperaUltimoNivel( $rsListaNivel );

        $obRCEMServico->setCodigoNivel( 1 );
        $obRCEMServico->setCodigoAtividade( $request->get("inCodAtividade") );
        $obRCEMServico->listarServico( $rsListaServico );

        if ( $rsListaServico->getNumLinhas() > 0 ) {

            $obRCEMServico->setValorreduzido( $rsListaServico->getCampo("valor_reduzido") );
            $obRCEMServico->setCodigoNivel( $rsListaNivel->getCampo("cod_nivel") );
            $obRCEMServico->listarServico( $rsListaServicoTMP );

            $stJs = 'f.stChaveServico.value = "'.$rsListaServicoTMP->getCampo("valor_reduzido").'";';
            sistemaLegado::executaFrameOculto($stJs);

            $this->obMontaServico->obRCEMServico->setCodigoNivel ( NULL  );
            $this->obMontaServico->setValorReduzidoServico ( $rsListaServicoTMP->getCampo("valor_reduzido") );
            $this->obMontaServico->preencheCombos();
        }

    }
    public function alterarServico(Request $request)
    {
        $stServico = $request->get('inIndice1');
        $flAliquota = $request->get('inIndice2');
        $flValorDeclarado = $request->get('inIndice3');
        $flDeducao = $request->get('inIndice4');
        $flDeducaoIncondicional = $request->get('inIndice5');
        $flValorLancado = $request->get('inIndice6');
        $flValorRetido = $request->get('inIndice7');
        $arServicoRetencao = Sessao::read( 'servicos_retencao' );
        $nregistros = count ( $arServicoRetencao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ( ( $arServicoRetencao[$inCount]["stServico"] == $stServico ) &&
                ( $arServicoRetencao[$inCount]["flAliquota"] == $flAliquota ) &&
                ( $arServicoRetencao[$inCount]["flValorDeclarado"] == $flValorDeclarado ) && ( $arServicoRetencao[$inCount]["flValorLancado"] == $flValorLancado ) && ( $arServicoRetencao[$inCount]["flDeducao"] == $flDeducao && $arServicoRetencao[$inCount]["flDeducaoIncondicional"] == $flDeducaoIncondicional)
                ) {

                $obTCGM = new TCGM;
                $obTCGM->setDado( "numcgm", $arServicoRetencao[$inCount]["inCGM"] );
                $obTCGM->recuperaPorChave( $rsCGM );
                if ( !$rsCGM->Eof() ) {
                    $stNomCgm = $rsCGM->getCampo("nom_cgm");
                    $stJs = 'd.getElementById("stCGM").innerHTML = "'.$stNomCgm.'";';
                }

                Sessao::write( 'servicos_retencao_alterando', $inCount+1 );

                $stJs .= 'f.stChaveServico.value = "'.$arServicoRetencao[$inCount]["stServico"].'";';

                if ($arServicoRetencao[$inCount]["flValorDeclarado"]) {
                    $stJs .= 'f.flAliquota.value = "'.$arServicoRetencao[$inCount]["flAliquota"].'";';

                    $stJs .= 'f.flValorDeclarado.value = "'.$arServicoRetencao[$inCount]["flValorDeclarado"].'";';

                    $stJs .= 'f.flDeducao.value = "'.$arServicoRetencao[$inCount]["flDeducao"].'";';
                $stJs .= 'f.flDeducaoIncondicional.value = "'.$arServicoRetencao[$inCount]["flDeducaoIncondicional"].'";';
                } else {
                    $stJs .= 'f.flValorRetido.value = "'.$arServicoRetencao[$inCount]["flValorRetido"].'";';

                    $stJs .= 'f.inCodigoUF.value = "'.$arServicoRetencao[$inCount]["stEstado"].'";';

                    $stJs .= 'f.inCodUF.value = "'.$arServicoRetencao[$inCount]["stEstado"].'";';

                    $stJs .= 'f.inCGM.value = "'.$arServicoRetencao[$inCount]["inCGM"].'";';

                    $stJs .= 'f.inCodigoMunicipio.value = "'.$arServicoRetencao[$inCount]["stMunicipio"].'";';

                    $stJs .= 'f.inCodMunicipio.value = "'.$arServicoRetencao[$inCount]["stMunicipio"].'";';
                }

                sistemaLegado::executaFrameOculto( $stJs );

                $this->obMontaServico->setCodigoVigenciaServico( $request->get("inCodigoVigencia")   );
                $this->obMontaServico->setCodigoNivelServico   ( $request->get("inCodigoNivel")      );
                $this->obMontaServico->setValorReduzidoServico ( $arServicoRetencao[$inCount]["stServico"] );
                $this->obMontaServico->preencheCombos();
                break;
            }
        }

    }

    public function excluirServico(Request $request)
    {
        $stServico = $request->get('inIndice1');
        $flAliquota = $request->get('inIndice2');
        $flValorDeclarado = $request->get('inIndice3');
        $flDeducao = $request->get('inIndice4');
        $flDeducaoIncondicional = $request->get('inIndice5');
        $flValorLancado = $request->get('inIndice6');
        $flValorRetido = $request->get('inIndice7');

        $arTmpServico = array();
        $inCountArray = 0;

        if ($request->get("stEscrituracao") == "nota") {
            if ($request->get("boReterFonte")) { //com retencao
                $arServicoRetencaoComRT = Sessao::read( 'servicos_retencao_comrt' );
                $nregistros = count ( $arServicoRetencaoComRT );
                for ($inCount = 0; $inCount < $nregistros; $inCount++) {
                    if ( ( $arServicoRetencaoComRT[$inCount]["stServico"] != $stServico ) ||
                        ( $arServicoRetencaoComRT[$inCount]["flAliquota"] != $flAliquota ) ||
                        ( $arServicoRetencaoComRT[$inCount]["flValorDeclarado"] != $flValorDeclarado ) || ( $arServicoRetencaoComRT[$inCount]["flValorLancado"] != $flValorLancado ) || ( $arServicoRetencaoComRT[$inCount]["flDeducao"] != $flDeducao ) || ( $arServicoRetencaoComRT[$inCount]["flDeducaoIncondicional"] != $flDeducaoIncondicional )
                        ) {
                        $arTmpServico[$inCountArray] = $arServicoRetencaoComRT[$inCount];
                        $inCountArray++;
                    }
                }

                Sessao::write( 'servicos_retencao_comrt', $arTmpServico );
            } else { //sem retencao
                $arServicoRetencaoSemRT = Sessao::read( 'servicos_retencao_semrt' );
                $nregistros = count ( $arServicoRetencaoSemRT );
                for ($inCount = 0; $inCount < $nregistros; $inCount++) {
                    if ( ( $arServicoRetencaoSemRT[$inCount]["stServico"] != $stServico ) ||
                        ( $arServicoRetencaoSemRT[$inCount]["flAliquota"] != $flAliquota ) ||
                        ( $arServicoRetencaoSemRT[$inCount]["flValorDeclarado"] != $flValorDeclarado ) || 					( $arServicoRetencaoSemRT[$inCount]["flValorLancado"] != $flValorLancado ) ||
                ( $arServicoRetencaoSemRT[$inCount]["flDeducao"] != $flDeducao) || ( $arServicoRetencaoSemRT[$inCount]["flDeducaoIncondicional"] != $flDeducaoIncondicional)
                        ) {
                        $arTmpServico[$inCountArray] = $arServicoRetencaoSemRT[$inCount];
                        $inCountArray++;
                    }
                }

                Sessao::write( 'servicos_retencao_semrt', $arTmpServico );
            }
        } else {
            $arServicoRetencao = Sessao::read( 'servicos_retencao' );
            $nregistros = count ( $arServicoRetencao );
            for ($inCount = 0; $inCount < $nregistros; $inCount++) {
                if ( ( $arServicoRetencao[$inCount]["stServico"] != $stServico ) ||
                    ( $arServicoRetencao[$inCount]["flAliquota"] != $flAliquota ) ||
                    ( $arServicoRetencao[$inCount]["flValorDeclarado"] != $flValorDeclarado ) || ( $arServicoRetencao[$inCount]["flValorLancado"] != $flValorLancado ) || ( $arServicoRetencao[$inCount]["flDeducao"] != $flDeducao ) || ( $arServicoRetencao[$inCount]["flDeducaoIncondicional"] != $flDeducaoIncondicional )
                    ) {
                    $arTmpServico[$inCountArray] = $arServicoRetencao[$inCount];
                    $inCountArray++;
                }
            }

            Sessao::write( 'servicos_retencao', $arTmpServico );
        }

        $rsListaServicos = new RecordSet;
        $rsListaServicos->preenche ( $arTmpServico );

        $stJs = $this->montaListaServicos ( $rsListaServicos );

        if ( count($arTmpServico) == 0) {

            $stJs .= "d.getElementById('boReterFonte').disabled=false;\n";
        }

        return sistemaLegado::executaFrameOculto( $stJs );

    }

    public function incluirServicoLista(Request $request)
    {
        if ($request->get("boReterFonte")) {
            if (!$request->get("stChaveServico")) {
                $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if (!$request->get("flValorRetido")) {
                $stJs = "alertaAviso('@Campo Valor Retido inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if (!$request->get("inCGM")) {
                $stJs = "alertaAviso('@Campo CGM - Retentor inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if (!$request->get("inCodigoUF")) {
                $stJs = "alertaAviso('@Campo Estado inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if (!$request->get("inCodigoMunicipio")) {
                $stJs = "alertaAviso('@Campo Município inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }
        } else { //sem retencao
            if (!$request->get("stChaveServico")) {
                $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if (!$request->get("flAliquota")) {
                $stJs = "alertaAviso('@Campo Alíquota inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            } else {
                $flAliquota = $request->get('flAliquota');
                $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $flAliquota ) );
                if ($flAliquota <= 0 || $flAliquota > 100) {
                    $stJs = "alertaAviso('@Valor da Aliquota inválido.','form','erro','".Sessao::getId()."');";
                    sistemaLegado::executaFrameOculto( $stJs );
                    exit;
                }
            }

            if (!$request->get("flValorDeclarado")) {
                $stJs = "alertaAviso('@Campo Valor Declarado inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }
                     if (!$request->get("flDeducao")) {
                $stJs = "alertaAviso('@Campo Dedução Legal inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }
                    if (!$request->get("flDeducaoIncondicional")) {
                $stJs = "alertaAviso('@Campo Dedução Incondicional inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }
        }

        if ($request->get("stEscrituracao") == "nota") {
            if ($request->get("boReterFonte")) { //com retencao
                $arServicoRetencaoComRT = Sessao::read( 'servicos_retencao_comrt' );
                for ( $inX=0; $inX<count( $arServicoRetencaoComRT ); $inX++) {
                    if ( Sessao::read( 'servicos_retencao_alterando_comrt' ) == ($inX+1) )
                        continue;

                    if ($arServicoRetencaoComRT[$inX]["stServico"] == $request->get("stChaveServico")) {
                        $stJs = "alertaAviso('O servico já está na lista.','form','erro','".Sessao::getId()."');";
                        $stJs .= 'f.stChaveServico.focus();';
                        sistemaLegado::executaFrameOculto( $stJs );
                        exit;
                    }
                }

                if ( Sessao::read( 'servicos_retencao_alterando_comrt' ) ) {
                    $inTotalElementos = Sessao::read( 'servicos_retencao_alterando_comrt' ) - 1;
                    Sessao::write( 'servicos_retencao_alterando_comrt', "" );

                    unset( $arServicoRetencaoComRT[$inTotalElementos]["flAliquota"]);
                    unset( $arServicoRetencaoComRT[$inTotalElementos]["flValorDeclarado"]);
                    unset( $arServicoRetencaoComRT[$inTotalElementos]["flValorLancado"]);
                    unset( $arServicoRetencaoComRT[$inTotalElementos]["flDeducao"]);
                unset( $arServicoRetencaoComRT[$inTotalElementos]["flDeducaoIncondicional"]);
                    Sessao::write( 'servicos_retencao_comrt', $arServicoRetencaoComRT );
                }else
                    $inTotalElementos = count ( $arServicoRetencaoComRT );
            } else { //sem retencao
                $arServicoRetencaoSemRT = Sessao::read( 'servicos_retencao_semrt' );
                for ( $inX=0; $inX<count ( $arServicoRetencaoSemRT ); $inX++) {
                    if ( Sessao::read( 'servicos_retencao_alterando_semrt' ) == ($inX+1) )
                        continue;

                    if ($arServicoRetencaoSemRT[$inX]["stServico"] == $request->get("stChaveServico")) {
                        $stJs = "alertaAviso('O servico já está na lista.','form','erro','".Sessao::getId()."');";
                        $stJs .= 'f.stChaveServico.focus();';
                        sistemaLegado::executaFrameOculto( $stJs );
                        exit;
                    }
                }

                if ( Sessao::read( 'servicos_retencao_alterando_semrt' ) ) {
                    $inTotalElementos = Sessao::read( 'servicos_retencao_alterando_semrt' ) - 1;
                    Sessao::write( 'servicos_retencao_alterando_semrt', "" );

                    unset( $arServicoRetencaoSemRT[$inTotalElementos]["flAliquota"] );
                    unset( $arServicoRetencaoSemRT[$inTotalElementos]["flValorDeclarado"] );
                    unset( $arServicoRetencaoSemRT[$inTotalElementos]["flValorLancado"] );
                    unset( $arServicoRetencaoSemRT[$inTotalElementos]["flDeducao"] );
                unset( $arServicoRetencaoSemRT[$inTotalElementos]["flDeducaoIncondicional"] );

                    Sessao::write( 'servicos_retencao_semrt', $arServicoRetencaoSemRT );
                }else
                    $inTotalElementos = count ( $arServicoRetencaoSemRT );
            }
        } else { //por servico
            $arServicoRetencao = Sessao::read( 'servicos_retencao' );
            for ( $inX=0; $inX<count ( $arServicoRetencao ); $inX++) {
                if ( Sessao::read( 'servicos_retencao_alterando' ) == ($inX+1) )
                    continue;

                if ($arServicoRetencao[$inX]["stServico"] == $request->get("stChaveServico")) {
                    if ($request->get("flValorDeclarado")) { //sem retencao
                        if ($arServicoRetencao[$inX]["flValorDeclarado"]) {
                            $stJs = "alertaAviso('O servico já está na lista.','form','erro','".Sessao::getId()."');";
                            $stJs .= 'f.stChaveServico.focus();';
                            sistemaLegado::executaFrameOculto( $stJs );
                            exit;
                        }
                    } else { //com retencao
                        if (!$arServicoRetencao[$inX]["flValorDeclarado"]) {
                            $stJs = "alertaAviso('O servico já está na lista.','form','erro','".Sessao::getId()."');";
                            $stJs .= 'f.stChaveServico.focus();';
                            sistemaLegado::executaFrameOculto( $stJs );
                            exit;
                        }
                    }
                }
            }

            if ( Sessao::read( 'servicos_retencao_alterando' ) ) {
                $inTotalElementos = Sessao::read( 'servicos_retencao_alterando' ) - 1;
                Sessao::write( 'servicos_retencao_alterando', "" );

                unset( $arServicoRetencao[$inTotalElementos]["flAliquota"] );
                unset( $arServicoRetencao[$inTotalElementos]["flValorDeclarado"] );
                unset( $arServicoRetencao[$inTotalElementos]["flValorLancado"] );
                unset( $arServicoRetencao[$inTotalElementos]["flDeducao"] );
            unset( $arServicoRetencao[$inTotalElementos]["flDeducaoIncondicional"] );

                Sessao::write( 'servicos_retencao', $arServicoRetencao );
            }else
                $inTotalElementos = count ( $arServicoRetencao );
        }

        $obTCEMServico = new TCEMServico;
        if ($request->get("boReterFonte")) {
            $stFiltro = " WHERE es.cod_estrutural = '".$request->get("stChaveServico")."' AND esa.cod_atividade = ".$request->get("inCodAtividade");

            $obTCEMServico->verificaServico( $rsListaServico, $stFiltro );
            if ( $rsListaServico->Eof() ) {
                $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if ($request->get("stEscrituracao") == "nota") {
                $arServicoRetencaoComRT = Sessao::read( 'servicos_retencao_comrt' );
                $arServicoRetencaoComRT[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
                $arServicoRetencaoComRT[$inTotalElementos]["stServico"] = $request->get("stChaveServico");
                $arServicoRetencaoComRT[$inTotalElementos]["flValorRetido"] = $request->get("flValorRetido");
                $arServicoRetencaoComRT[$inTotalElementos]["inCGM"] = $request->get("inCGM");
                $arServicoRetencaoComRT[$inTotalElementos]["stEstado"] = $request->get("inCodigoUF");
                $arServicoRetencaoComRT[$inTotalElementos]["stMunicipio"] = $request->get("inCodigoMunicipio");
                Sessao::write( 'servicos_retencao_comrt', $arServicoRetencaoComRT );
            } else {
                $arServicoRetencao = Sessao::read( 'servicos_retencao' );
                $arServicoRetencao[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
                $arServicoRetencao[$inTotalElementos]["stServico"] = $request->get("stChaveServico");
                $arServicoRetencao[$inTotalElementos]["flValorRetido"] = $request->get("flValorRetido");
                $arServicoRetencao[$inTotalElementos]["inCGM"] = $request->get("inCGM");
                $arServicoRetencao[$inTotalElementos]["stEstado"] = $request->get("inCodigoUF");
                $arServicoRetencao[$inTotalElementos]["stMunicipio"] = $request->get("inCodigoMunicipio");
                Sessao::write( 'servicos_retencao', $arServicoRetencao );
            }

            $stJs = 'f.stChaveServico.value = "";';
            $stJs .= 'f.flValorRetido.value = "";';
            $stJs .= 'f.inCodigoUF.value = "";';
            $stJs .= 'f.inCodUF.value = "";';
            $stJs .= 'f.inCGM.value = "";';
            $stJs .= "d.getElementById('stCGM').innerHTML = '&nbsp;';\n";
            $stJs .= 'f.inCodigoMunicipio.value = "";';
            $stJs .= 'f.inCodMunicipio.value = "";';

            $inX = 0;
            while ($request->getAll()) {
                $inX++;
                $stNome = "inCodServico_".$inX;
                if ($request->get( $stNome )) {
                    if ($inX > 1) {
                        $stJs .= "limpaSelect(f.".$stNome.",1); \n";
                        $stJs .= "f.".$stNome."[0] = new Option('Selecione Sub Grupo','', 'selected');\n";
                    }

                    $stJs .= 'f.'.$stNome.'.value = "";';
                }else
                    break;
            }
        } else { //sem retencao
            $stFiltro = " WHERE es.cod_estrutural = '".$request->get("stChaveServico")."' AND esa.cod_atividade = ".$request->get("inCodAtividade");
            $obTCEMServico = new TCEMServico;
            $obTCEMServico->verificaServico( $rsListaServico, $stFiltro );
            if ( $rsListaServico->Eof() ) {
                $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if ($request->get("stEscrituracao") == "nota") {
                $arServicoRetencaoSemRT = Sessao::read( 'servicos_retencao_semrt' );
                $arServicoRetencaoSemRT[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
                $arServicoRetencaoSemRT[$inTotalElementos]["stServico"] = $request->get("stChaveServico");
                $arServicoRetencaoSemRT[$inTotalElementos]["flAliquota"] = $request->get("flAliquota");
                $arServicoRetencaoSemRT[$inTotalElementos]["flValorDeclarado"] = $request->get("flValorDeclarado");

                if ($request->get("flDeducao"))
                    $arServicoRetencaoSemRT[$inTotalElementos]["flDeducao"] = $request->get("flDeducao");
                $arServicoRetencaoSemRT[$inTotalElementos]["flDeducaoIncondicional"] = $request->get("flDeducaoIncondicional");

                $flValorDeclarado = $request->get('flValorDeclarado');
                $flValorDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $flValorDeclarado ) );

                $flDeducao = $request->get('flDeducao');
                $flDeducao = str_replace ( ',', '.', str_replace ( '.', '', $flDeducao ) );
                
                $flDeducaoIncondicional = $request->get('flDeducaoIncondicional');
                $flDeducaoIncondicional = str_replace ( ',', '.', str_replace ( '.', '', $flDeducaoIncondicional ) );

                $flAliquota = $request->get('flAliquota');
                $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $flAliquota ) );

                $arServicoRetencaoSemRT[$inTotalElementos]["flValorLancado"] = ( $flValorDeclarado - $flDeducao + $flDeducaoIncondicional) ;
                $arServicoRetencaoSemRT[$inTotalElementos]["flValorLancado"] = number_format( $arServicoRetencaoSemRT[$inTotalElementos]["flValorLancado"], 2, ',', '.' );

                Sessao::write( 'servicos_retencao_semrt', $arServicoRetencaoSemRT );
            } else {
                $arServicoRetencao = Sessao::read( 'servicos_retencao' );

                $arServicoRetencao[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
                $arServicoRetencao[$inTotalElementos]["stServico"] = $request->get("stChaveServico");
                $arServicoRetencao[$inTotalElementos]["flAliquota"] = $request->get("flAliquota");
                $arServicoRetencao[$inTotalElementos]["flValorDeclarado"] = $request->get("flValorDeclarado");

                if ($request->get("flDeducao"))
                    $arServicoRetencao[$inTotalElementos]["flDeducao"] = $request->get("flDeducao");
                $arServicoRetencao[$inTotalElementos]["flDeducaoIncondicional"] = $request->get("flDeducaoIncondicional");

                $flValorDeclarado = $request->get("flValorDeclarado");
                $flValorDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $flValorDeclarado ) );
                $flDeducao = $request->get("flDeducao");
                $flDeducao = str_replace ( ',', '.', str_replace ( '.', '', $flDeducao ) );
                $flDeducaoIncondicional = $request->get("flDeducaoIncondicional");
                $flDeducaoIncondicional = str_replace ( ',', '.', str_replace ( '.', '', $flDeducaoIncondicional ) );
                $flAliquota = $request->get("flAliquota");
                $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $flAliquota ) );
                
                $arServicoRetencao[$inTotalElementos]["flValorLancado"] = ( $flValorDeclarado - ($flDeducao + $flDeducaoIncondicional));
                $arServicoRetencao[$inTotalElementos]["flValorLancado"] = number_format( $arServicoRetencao[$inTotalElementos]["flValorLancado"], 2, ',', '.' );
                Sessao::write( 'servicos_retencao', $arServicoRetencao );
            }

            $stJs = 'f.stChaveServico.value = "";';
            $stJs .= 'f.flAliquota.value = "";';
            $stJs .= 'f.flValorDeclarado.value = "";';
            $stJs .= 'f.flDeducao.value = "";';
            $stJs .= 'f.flDeducaoIncondicional.value = "";';

            $inX = 0;
            while ($request->getAll()) {
                $inX++;
                $stNome = "inCodServico_".$inX;
                if ($request->get( $stNome )) {
                    if ($inX > 1) {
                        $stJs .= "limpaSelect(f.".$stNome.",1); \n";
                        $stJs .= "f.".$stNome."[0] = new Option('Selecione Sub Grupo','', 'selected');\n";
                    }

                    $stJs .= 'f.'.$stNome.'.value = "";';
                }else
                    break;
            }
        }

        $rsListaServicos = new RecordSet;
        if ($request->get("stEscrituracao") == "nota") {
            if ($request->get("boReterFonte")) {

                $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao_comrt' ) );
            } else {
                $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao_semrt' ) );
            }
        } else {
            $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao' ) );
        }

        $stJs .= $this->montaListaServicos ( $rsListaServicos );
        $boTemValores = false;
        $arServicoRetencao = Sessao::read( 'servicos_retencao' );
        for ( $inContaRetencao=0; $inContaRetencao<count( $arServicoRetencao ); $inContaRetencao++ ) {
            if ($arServicoRetencao[$inContaRetencao]["flValorDeclarado"]) {
                $boTemValores = true;
                break;
            }
        }

        return sistemaLegado::executaFrameOculto( $stJs );

    }
    public function preencheProxComboServico(Request $request)
    {
        $stNomeComboServico = "inCodServico_".( $request->get("inPosicao") - 1);
        $stChaveLocal = $request->get($stNomeComboServico])
        $inPosicao = $request->get("inPosicao");
        if ( empty( $stChaveLocal ) and $request->get("inPosicao") > 2 ) {
            $stNomeComboServico = "inCodServico_".( $request->get("inPosicao") - 2);
            $stChaveLocal = $request->get($stNomeComboServico])
            $inPosicao = $request->get("inPosicao") - 1;
        }

        $arChaveLocal = explode("-" , $stChaveLocal );
        $this->obMontaServico->setCodigoVigenciaServico    ( $request->get("inCodigoVigencia") );
        $this->obMontaServico->setCodigoNivelServico       ( $arChaveLocal[0] );
        $this->obMontaServico->setCodigoServico            ( $arChaveLocal[1] );
        $this->obMontaServico->setValorReduzidoServico     ( $arChaveLocal[3] );
        $this->obMontaServico->preencheProxCombo           ( $inPosicao , $request->get("inNumNiveisServico") );

    }
    public function preencheCombosServico(Request $request)
    {
    $stFiltro = " WHERE es.cod_estrutural = '".$request->get("stChaveServico")."' AND esa.cod_atividade = ".$request->get("inCodAtividade");
        $obTCEMServico = new TCEMServico;
        $obTCEMServico->verificaServico( $rsListaServico, $stFiltro );
        if ( $rsListaServico->Eof() ) {
            $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.stChaveServico.value = "";';
            $stJs .= 'f.stChaveServico.focus();';

            $inX = 0;
            while ($request->getAll()) {
                $inX++;
                $stNome = "inCodServico_".$inX;
                if ($request->get( $stNome )) {
                    if ($inX > 1) {
                        $stJs .= "limpaSelect(f.".$stNome.",1); \n";
                        $stJs .= "f.".$stNome."[0] = new Option('Selecione Sub Grupo','', 'selected');\n";
                    }

                    $stJs .= 'f.'.$stNome.'.value = "";';
                }else
                    break;
            }

            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $this->obMontaServico->setCodigoVigenciaServico( $request->get("inCodigoVigencia")   );
        $this->obMontaServico->setCodigoNivelServico   ( $request->get("inCodigoNivel")      );
        $this->obMontaServico->setValorReduzidoServico ( $request->get("stChaveServico") );

        $this->obMontaServico->preencheCombos();

    }

}
