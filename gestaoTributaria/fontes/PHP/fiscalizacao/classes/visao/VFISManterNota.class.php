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
    * Data de Criação   : 18/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo Vaconcellos de Magalhães

    * @package URBEM
    * @subpackage Visao

*/
require_once( CAM_GT_FIS_NEGOCIO.'RFISManterNota.class.php' );
include_once ( CAM_GT_FIS_VISAO."VFISManterServico.class.php" );
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMServico.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );

class VFISManterNota
{
private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->obVFISManterServico = new VFISManterServico(null);
        $this->obMontaServico = new MontaServico;
                $this->obMontaServico->setCodigoAtividade( $request->get("inCodAtividade") );
                $this->obMontaServico->setCodigoVigenciaServico ( $request->get("inCodigoVigencia") );
    }

    public function incluirNota($param)
    {
                $boCompetencia = $this->obVFISManterServico->alteraCompetencia($param);
                $boExercicio = $this->validaExercicio($param);
                if ($boExercicio == 'false' or $boCompetencia == 'false') {

                } else {
                  return $this->controller->incluir($param);
                }
        }

    public function montaListaNotas($rsLista)
    {
         if ( $rsLista->getNumLinhas() > 0 ) {

        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsLista );
        $obLista->setTitulo                    ( "Lista de Notas" );
        $obLista->setTotaliza                  ( "flValorLancado,Valor em Serviço,right,8" );

        $obLista->setMostraPaginacao           ( false );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Série"                );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Nº da Nota"           );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Data de Emissão"      );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Alíquotas (%)"        );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Total de Serviços"    );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Retido"         );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Lançado"        );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inSerie"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inNumeroNota"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "dtEmissao"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flAliquota"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flTotalServico"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorRetido"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorLancado"  );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirNota();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice1", "inSerie" );
        $obLista->ultimaAcao->addCampo         ( "inIndice2", "inNumeroNota" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
        } else {
        $stHTML = "&nbsp;";
        }

        $js = "d.getElementById('spnListaNota').innerHTML = '".$stHTML."';\n";

    return $js;
    }

    public function montaListaServicos($rsLista)
    {
                $js = $this->obVFISManterServico->montaListaServicos($rsLista,false);

                return $js;
    }

    public function PreencheCGM(Request $request)
    {
        return 	$this->obVFISManterServico->PreencheCGM($request);
    }

    public function limpaNota(Request $request)
    {
        $rsListaNotas = new RecordSet;
        if ($request->get("boReter")) {
            Sessao::write( 'notas_retencao_comrt', array() );
        } else {
            Sessao::write( 'notas_retencao_semrt', array() );
        }
                Sessao::write( 'notas_retencao', array() );
        $stJs = $this->montaListaNotas ( $rsListaNotas );

    return sistemaLegado::executaFrameOculto($stJs);
    }

    public function limpaServico(Request $request)
    {
        return $this->obVFISManterServico->limpaServico($request);
    }
        public function limpaServicoLista(Request $request)
        {
            return $this->obVFISManterServico->limpaServicoLista($request);

        }

    public function preencheMunicipio(Request $request)
    {
        return $this->obVFISManterServico->preencheMunicipio($request);
    }

    public function validaData(Request $request)
    {
        return $this->obVFISManterServico->validaData($request);
    }

    public function alteraCompetencia(Request $request)
    {
        return $this->obVFISManterServico->alteraCompetencia($request);

    }

    public function LimparFormulario(Request $request)
    {
        return $this->obVFISManterServico->LimparFormulario($request);

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
            $obTxtCodUF->setName               ( "inCodigoUF"            );
                    $obTxtCodUF->setTitle              ( "Estado onde ocorreu a retenção" );
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
                    $obCmbUF->setTitle              ( "Estado onde ocorreu a retenção" );
            $obCmbUF->preencheCombo         ( $rsUF                   );
            $obCmbUF->setValue              ( $inCodigoUF             );
            $obCmbUF->setNull               ( false                   );
            $obCmbUF->setStyle              ( "width: 220px"          );
            $obCmbUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio')" );

            $obTxtCodMunicipio = new TextBox;
            $obTxtCodMunicipio->setRotulo    ( "Munic&iacute;pio"  );
            $obTxtCodMunicipio->setName      ( "inCodigoMunicipio" );
            $obTxtCodMunicipio->setValue     ( $inCodigoMunicipio  );
            $obTxtCodMunicipio->setSize      ( 8                   );
            $obTxtCodMunicipio->setMaxLength ( 8                   );
            $obTxtCodMunicipio->setNull      ( false               );
                    $obTxtCodMunicipio->setTitle     ( "Municipio onde ocorreu a retenção" );

            $obCmbMunicipio = new Select;
            $obCmbMunicipio->setName       ( "inCodMunicipio"   );
            $obCmbMunicipio->addOption     ( "", "Selecione"    );
            $obCmbMunicipio->setCampoId    ( "cod_municipio"    );
            $obCmbMunicipio->setCampoDesc  ( "nom_municipio"    );
            $obCmbMunicipio->setValue      ( $inCodigoMunicipio );
                    $obCmbMunicipio->setTitle      ( "Municipio onde ocorreu a retenção" );
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

                    $obHdnRetencao =  new Hidden;
                    $obHdnRetencao->setName   ( "stRetencao" );
                    $obHdnRetencao->setValue  ( "true"  );

            $obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF );
            $obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
            $obFormulario->addComponente ( $obBscCGM );
                    $obFormulario->addHidden( $obHdnRetencao );

            $this->obMontaServico = new MontaServico;
            $this->obMontaServico->setCodigoAtividade( $request->get("inCodAtividade") );
            $this->obMontaServico->setCodigoVigenciaServico ( $request->get("inCodigoVigencia") );
            $this->obMontaServico->geraFormulario( $obFormulario );

            $obFormulario->addComponente ( $obTxtValorRetido );

        } else {
            //sem retencao

            $obTxtAliquota = new TextBox;
            $obTxtAliquota->setRotulo ( "Alíquota (%)" );
                    $obTxtAliquota->setTitle ( "Alíquota a incidir sobre o serviço" );
            $obTxtAliquota->setName ( "flAliquota" );
            $obTxtAliquota->setId ( "flAliquota" );
                    $obTxtAliquota->setInteiro ( true );
            $obTxtAliquota->setNull ( false );
            $obTxtAliquota->setNaoZero ( true );
            $obTxtAliquota->setSize ( 6 );
            $obTxtAliquota->setMaxLength ( 6 );

                    $obHdnRetencao =  new Hidden;
                    $obHdnRetencao->setName   ( "stRetencao" );
                    $obHdnRetencao->setValue  ( "false"  );

            $obTxtValorDeclarado = new Numerico;
            $obTxtValorDeclarado->setRotulo ( "Valor Declarado" );
            $obTxtValorDeclarado->setName ( "flValorDeclarado" );
            $obTxtValorDeclarado->setId ( "flValorDeclarado" );
            $obTxtValorDeclarado->setDecimais ( 2 );
                    $obTxtValorDeclarado->setTitle( "Valor declarado para o serviço" );
            $obTxtValorDeclarado->setMaxValue ( 99999999999999.99 );
            $obTxtValorDeclarado->setNull ( false );
            $obTxtValorDeclarado->setNegativo ( false );
            $obTxtValorDeclarado->setNaoZero ( true );
            $obTxtValorDeclarado->setSize ( 20 );
            $obTxtValorDeclarado->setMaxLength ( 20 );

            $obTxtDeducao = new Numerico;
            $obTxtDeducao->setRotulo ( "Dedução Incondicional" );
            $obTxtDeducao->setTitle ( "Descontos." );
            $obTxtDeducao->setName ( "flDeducao" );
            $obTxtDeducao->setId ( "flDeducao" );
                    $obTxtDeducao->setTitle ( "Deduções que possam incidir sobre o serviço" );
            $obTxtDeducao->setDecimais ( 2 );
            $obTxtDeducao->setMaxValue ( 99999999999999.99 );
            $obTxtDeducao->setNull ( true );
            $obTxtDeducao->setNegativo ( false );
            $obTxtDeducao->setNaoZero ( false );
            $obTxtDeducao->setSize ( 20 );
            $obTxtDeducao->setMaxLength ( 20 );
                    $obFormulario->addTitulo     ( "Dados para Serviço" );
            $this->obMontaServico = new MontaServico;
            $this->obMontaServico->setCodigoAtividade( $request->get("inCodAtividade") );
            $this->obMontaServico->setCodigoVigenciaServico ( $request->get("inCodigoVigencia") );
            $this->obMontaServico->geraFormulario( $obFormulario );

            $obFormulario->addComponente( $obTxtAliquota );
            $obFormulario->addComponente( $obTxtValorDeclarado );
            $obFormulario->addComponente( $obTxtDeducao );
                    $obFormulario->addHidden( $obHdnRetencao );
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
        $stJs2 .= $this->montaListaNotas ( $rsListaNotas );

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
        return $this->obVFISManterServico->alterarServico($request);

    }

    public function excluirNota(Request $request)
    {
        $inSerie = $request->get('inIndice1');
        $inNumeroNota = $request->get('inIndice2');
        if ($request->get("boReter")) { //com retencao
            $arTmpServico = array();
            $inCountArray = 0;
            $arNotasRetencaoComRT = Sessao::read( 'notas_retencao_comrt' );
            $nregistros = count ( $arNotasRetencaoComRT );
            for ($inCount = 0; $inCount < $nregistros; $inCount++) {
                if ($arNotasRetencaoComRT[$inCount]["inNumeroNota"] != $inNumeroNota ||
                    $arNotasRetencaoComRT[$inCount]["inSerie"] != $inSerie) {
                    $arTmpServico[$inCountArray] = $arNotasRetencaoComRT[$inCount];
                    $inCountArray++;
                }
            }

            Sessao::write( 'notas_retencao_comrt', $arTmpServico );
        } else {
            $arTmpServico = array();
            $inCountArray = 0;
            $arNotasRetencaoSemRT = Sessao::read( 'notas_retencao_semrt' );
            $nregistros = count ( $arNotasRetencaoSemRT );
            for ($inCount = 0; $inCount < $nregistros; $inCount++) {
                if ($arNotasRetencaoSemRT[$inCount]["inNumeroNota"] != $inNumeroNota ||
                    $arNotasRetencaoSemRT[$inCount]["inSerie"] != $inSerie) {
                    $arTmpServico[$inCountArray] = $arNotasRetencaoSemRT[$inCount];
                    $inCountArray++;
                }
            }

            Sessao::write( 'notas_retencao_semrt', $arTmpServico );
        }

        $rsListaNotas = new RecordSet;
        $rsListaNotas->preenche ( $arTmpServico );

        $stJs = $this->montaListaNotas ( $rsListaNotas );
        if ( count($arTmpServico) == 0) {

            $stJs .= "d.getElementById('boReterFonte').disabled=false;\n";
        }
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

    public function excluirServico(Request $request)
    {
        return $this->obVFISManterServico->excluirServico($request);

    }

    public function incluirNotaLista(Request $request)
    {
        if (!$request->get("dtEmissao")) {
            $stJs = "alertaAviso('@Campo Data da Emissão vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$request->get("inNumeroNota")) {
            $stJs = "alertaAviso('@Campo Número da Nota vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$request->get("inSerie")) {
            $stJs = "alertaAviso('@Campo Série vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if ($request->get("boReter")) {

            $nregistros = count ( Sessao::read('servicos_retencao') );
            if ($nregistros <= 0) {
                $stJs = "alertaAviso('@Campo Lista de Serviços vazia.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            $arNotasRetencaoComRT = Sessao::read( 'notas_retencao_comrt' );
            $nroNotas = count ( $arNotasRetencaoComRT );
            $boIncluir = true;
            for ($inX=0; $inX<$nroNotas; $inX++) {
                if ($arNotasRetencaoComRT[$inX]["inNumeroNota"] == $request->get("inNumeroNota")
                    && $arNotasRetencaoComRT[$inX]["inSerie"] == $request->get("inSerie")) {
                    $stJs = "alertaAviso('@A nota já está na lista.','form','erro','".Sessao::getId()."');";
                    $boIncluir = false;
                    break;
                }
            }
        } else { //sem rentencao

            $nregistros = count ( Sessao::read( 'servicos_retencao' ) );
            if ($nregistros <= 0) {
                $stJs = "alertaAviso('@Campo Lista de Serviços vazia.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            $arNotasRetencaoSemRT = Sessao::read( 'notas_retencao_semrt' );
            $nroNotas = count ( $arNotasRetencaoSemRT );
            $boIncluir = true;
            for ($inX=0; $inX<$nroNotas; $inX++) {
                if ($arNotasRetencaoSemRT[$inX]["inNumeroNota"] == $request->get("inNumeroNota") && $arNotasRetencaoSemRT[$inX]["inSerie"] == $request->get("inSerie")) {
                    $stJs = "alertaAviso('@A nota já está na lista.','form','erro','".Sessao::getId()."');";
                    $boIncluir = false;
                    break;
                }
            }
        }

        if ($boIncluir) {
            $flTotalLancado = 0;
            $flTotalServico = 0;
            $flTotalRetido = 0;
            $stAliquota = "";

            $arServicoRetencao = Sessao::read( 'servicos_retencao' );

            for ($inX=0; $inX<$nregistros; $inX++) {
                if ($request->get("boReterFonte")) { //com retencao
                    $flTotalLancado += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao[$inX]["flValorLancado"] ) );
                    $flTotalServico += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao[$inX]["flValorDeclarado"] ) );
                    $flTotalRetido += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao[$inX]["flValorRetido"] ));
                } else { //sem retencao
                    $flTotalLancado += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao[$inX]["flValorLancado"] ) );
                    $flTotalServico += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao[$inX]["flValorDeclarado"] ) );
                    $flTotalRetido += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao[$inX]["flValorRetido"] ));

                    if ($arServicoRetencao[$inX]["flAliquota"]) {
                        $stAliquota .= $arServicoRetencao[$inX]["flAliquota"];
                        if ($nregistros != 1) {
                                   $stAliquota .= ";";
                                }
                    }
                }
            }

            $rsListaServicos = new RecordSet;
            $rsListaNotas = new RecordSet;

            if ($request->get("boReter")) { //com retencao
                $arNotasRetencaoComRT = Sessao::read( 'notas_retencao_comrt' );
                $arNotasRetencaoComRT[$nroNotas]["flValorMercadoria"] = $request->get("flValorMercadoria");
                $arNotasRetencaoComRT[$nroNotas]["inNumeroNota"] = $request->get("inNumeroNota");
                $arNotasRetencaoComRT[$nroNotas]["inSerie"] = $request->get("inSerie");
                $arNotasRetencaoComRT[$nroNotas]["dtEmissao"] = $request->get("dtEmissao");
                $arNotasRetencaoComRT[$nroNotas]["flTotalServico"] = number_format( $flTotalServico, 2, ',', '.' );
                $arNotasRetencaoComRT[$nroNotas]["flValorLancado"] = number_format( $flTotalLancado, 2, ',', '.' );
                $arNotasRetencaoComRT[$nroNotas]["flValorRetido"] = number_format( $flTotalRetido, 2, ',', '.' );
                $arNotasRetencaoComRT[$nroNotas]["arServicos"] = Sessao::read( 'servicos_retencao' );
                Sessao::write( 'notas_retencao_comrt', $arNotasRetencaoComRT );
                Sessao::write( 'servicos_retencao_comrt', array() );

                $rsListaServicos->preenche ( array() );
                $rsListaNotas->preenche ( $arNotasRetencaoComRT );
            } else { //sem retencao
                $arNotasRetencaoSemRT = Sessao::read( 'notas_retencao_semrt' );
                        echo $nroNotas;
                $arNotasRetencaoSemRT[$nroNotas]["flValorMercadoria"] = $request->get("flValorMercadoria");
                $arNotasRetencaoSemRT[$nroNotas]["inNumeroNota"] = $request->get("inNumeroNota");
                $arNotasRetencaoSemRT[$nroNotas]["inSerie"] = $request->get("inSerie");
                $arNotasRetencaoSemRT[$nroNotas]["dtEmissao"] = $request->get("dtEmissao");
                $arNotasRetencaoSemRT[$nroNotas]["flAliquota"] = $stAliquota;
                $arNotasRetencaoSemRT[$nroNotas]["flTotalServico"] = number_format( $flTotalServico, 2, ',', '.' );
                $arNotasRetencaoSemRT[$nroNotas]["flValorLancado"] = number_format( $flTotalLancado, 2, ',', '.' );
                $arNotasRetencaoSemRT[$nroNotas]["flValorRetido"] = number_format( $flTotalRetido, 2, ',', '.' );
                $arNotasRetencaoSemRT[$nroNotas]["arServicos"] = Sessao::read( 'servicos_retencao' );

                Sessao::write( 'notas_retencao_semrt', $arNotasRetencaoSemRT );
                Sessao::write( 'servicos_retencao_semrt', array() );
                $rsListaServicos->preenche ( array() );
                $rsListaNotas->preenche ( $arNotasRetencaoSemRT );

            }

            $stJs = $this->montaListaServicos ( $rsListaServicos );
            $stJs .= $this->montaListaNotas ( $rsListaNotas );

            $stJs .= 'f.dtEmissao.value = "";';
            $stJs .= 'f.inNumeroNota.value = "";';
            $stJs .= 'f.inSerie.value = "";';
                    $stJs .= "d.getElementById('botaoNota').innerHTML = '';\n";
            $boTemValores = false;
            $arServicoRetencao = Sessao::read( 'servicos_retencao' );
            for ( $inContaRetencao=0; $inContaRetencao<count( $arServicoRetencao ); $inContaRetencao++ ) {
                if ($arServicoRetencao[$inContaRetencao]["flValorDeclarado"]) {
                    $boTemValores = true;
                    break;
                }
            }
                    #ajuste de regra de sessão
                    Sessao::write( 'servicos_retencao', array() );

        }

        sistemaLegado::executaFrameOculto( $stJs );
    }

    public function validaExercicio(Request $request)
    {
        $dtInicio      = $request->get('inInicio');
        $dtTermino     = $request->get('inTermino');
        $stExercicio   = $request->get('stExercicio');
        $stCompetencia = $request->get('stCompetencia');

        $arDataInicio  = explode("-",$dtInicio);
        $arDataTermino = explode("-",$dtTermino);

        if ($arDataInicio[0] > $stExercicio or
             $arDataTermino[0] < $stExercicio) {

            $stJs = "alertaAviso('@ Exercício fora do período de fiscalização. Período válido de ".$arDataInicio[1]."/".$arDataInicio[0]." à ".$arDataTermino[1]."/".$arDataTermino[0]."','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.stExercicio.value = "";';
            $stJs .= 'f.stExercicio.focus()';

            sistemaLegado::executaFrameOculto( $stJs );

            return 'false';
        }

        return 'true';
    }

    public function incluirServicoLista(Request $request)
    {
          if ($request->get("stRetencao") =="true") {
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
                $flAliquota = $request->get("flAliquota");
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
        }

        if ($request->get("stEscrituracao") == "nota") {
            if ($request->get("stRetencao") =="true") { //com retencao
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

                Sessao::write( 'servicos_retencao', $arServicoRetencao );
            }else
                $inTotalElementos = count ( $arServicoRetencao );
        }

        $obTCEMServico = new TCEMServico;
        if ($request->get("stRetencao") =="true") {
            $stFiltro = " WHERE es.cod_estrutural = '".$request->get("stChaveServico")."' AND esa.cod_atividade = ".$request->get("inCodAtividade");
            $obTCEMServico = new TCEMServico;
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

                $flValorDeclarado = $request->get("flValorDeclarado");
                $flValorDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $flValorDeclarado ) );
                $flDeducao = $request->get("flDeducao");
                $flDeducao = str_replace ( ',', '.', str_replace ( '.', '', $flDeducao ) );
                $flAliquota = $request->get("flAliquota");
                $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $flAliquota ) );

                $arServicoRetencaoSemRT[$inTotalElementos]["flValorLancado"] = ( $flValorDeclarado - $flDeducao );
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
                
                $flValorDeclarado = $request->get("flValorDeclarado");
                $flValorDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $flValorDeclarado ) );
                $flDeducao = $request->get("flDeducao");                
                $flDeducao = str_replace ( ',', '.', str_replace ( '.', '', $flDeducao ) );
                $flAliquota = $request->get("flAliquota");                
                $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $flAliquota ) );
                $arServicoRetencao[$inTotalElementos]["flValorLancado"] = ( $flValorDeclarado - $flDeducao );
                $arServicoRetencao[$inTotalElementos]["flValorLancado"] = number_format( $arServicoRetencao[$inTotalElementos]["flValorLancado"], 2, ',', '.' );
                Sessao::write( 'servicos_retencao', $arServicoRetencao );
            }

            $stJs = 'f.stChaveServico.value = "";';
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
                $obFormulario = new Formulario;
                //botoes Nota
                $obBtnIncluirNota = new Button;
                $obBtnIncluirNota->setName              ( "btnIncluirNota" );
                $obBtnIncluirNota->setValue             ( "Incluir" );
                $obBtnIncluirNota->setTipo              ( "button" );
                $obBtnIncluirNota->obEvento->setOnClick ( "incluirNotaLista();" );
                $obBtnIncluirNota->setDisabled          ( false );

                $botoesNota = array ( $obBtnIncluirNota );
                $obFormulario->defineBarra ( $botoesNota, 'left', '' );
                $obFormulario->montaInnerHTML();
                $stJs .= "d.getElementById('botaoNota').innerHTML = '". $obFormulario->getHTML(). "';\n";

       return sistemaLegado::executaFrameOculto( $stJs );

    }

    public function preencheProxComboServico(Request $request)
    {
        return $this->obVFISManterServico->preencheProxComboServico($request);
    }

    public function preencheCombosServico(Request $request)
    {
        return $this->obVFISManterServico->preencheCombosServico($request);
    }

}
