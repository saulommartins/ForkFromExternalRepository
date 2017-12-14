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
require_once( CAM_GT_FIS_NEGOCIO.'RFISManterLevantamento.class.php' );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );

class VFISManterLevantamento
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function mostraServico()
    {
        $obForm = new Form();

               //Define o formulario
            $obFormulario = new Formulario;
            $obFormulario->addForm ( $obForm );

        if ($_GET['boFaturamento']== '') {

            $obMontaServico = new MontaServico;
            $obMontaServico->setCadastroServico( false );
            $obMontaServico->setRotulo( "*Serviço" );
            $obMontaServico->geraFormulario($obFormulario);

            $obTxtAliquota = new Numerico;
            $obTxtAliquota->setRotulo ( "*Alíquota (%)" );
            $obTxtAliquota->setName ( "flAliquota" );
            $obTxtAliquota->setId ( "flAliquota" );
            $obTxtAliquota->setDecimais ( 2 );
            $obTxtAliquota->setNull ( true );
            $obTxtAliquota->setNegativo ( false );
            $obTxtAliquota->setNaoZero ( true );
            $obTxtAliquota->setSize ( 6 );
            $obTxtAliquota->setMaxLength ( 6 );

            $obTxtValorDeclarado = new Numerico;
            $obTxtValorDeclarado->setRotulo ( "*Valor Declarado" );
            $obTxtValorDeclarado->setName ( "flValorDeclarado" );
            $obTxtValorDeclarado->setId ( "flValorDeclarado" );
            $obTxtValorDeclarado->setDecimais ( 2 );
            $obTxtValorDeclarado->setMaxValue ( 99999999999999.99 );
            $obTxtValorDeclarado->setNull ( true );
            $obTxtValorDeclarado->setNegativo ( false );
            $obTxtValorDeclarado->setNaoZero ( true );
            $obTxtValorDeclarado->setSize ( 20 );
            $obTxtValorDeclarado->setMaxLength ( 20 );

            $obTxtDeducao = new Numerico;
            $obTxtDeducao->setRotulo ( "Dedução" );
            $obTxtDeducao->setName ( "flDeducao" );
            $obTxtDeducao->setId ( "flDeducao" );
            $obTxtDeducao->setDecimais ( 2 );
            $obTxtDeducao->setMaxValue ( 99999999999999.99 );
            $obTxtDeducao->setNull ( true );
            $obTxtDeducao->setNegativo ( false );
            $obTxtDeducao->setNaoZero ( true );
            $obTxtDeducao->setSize ( 20 );
            $obTxtDeducao->setMaxLength ( 20 );

            $obFormulario->addComponente($obTxtAliquota);
            $obFormulario->addComponente($obTxtValorDeclarado);
            $obFormulario->addComponente($obTxtDeducao);
            $obFormulario->montaInnerHTML();
            if ($this->controller==null)
                return  $obFormulario->getHTML();
            $stJs = "$('spnServico').innerHTML = '".$obFormulario->getHTML()."';";

        } else {
            $rsUF = new RecordSet;
            $obRCIMLogradouro = new RCIMLogradouro;
            $obRCIMLogradouro->listarUF( $rsUF );

            $rsMunicipios = new RecordSet;

            $obTxtCodUF = new TextBox;
            $obTxtCodUF->setRotulo             ( "*Estado"               );
            $obTxtCodUF->setName               ( "inCodigoUF"            );
            $obTxtCodUF->setValue              ( $inCodigoUF             );
            $obTxtCodUF->setSize               ( 8                       );
            $obTxtCodUF->setMaxLength          ( 8                       );
            $obTxtCodUF->setNull               ( true                    );
            $obTxtCodUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio')" );

            $obCmbUF = new Select;
            $obCmbUF->setName               ( "inCodUF"               );
            $obCmbUF->addOption             ( "", "Selecione"         );
            $obCmbUF->setCampoId            ( "cod_uf"                );
            $obCmbUF->setCampoDesc          ( "nom_uf"                );
            $obCmbUF->preencheCombo         ( $rsUF                   );
            $obCmbUF->setValue              ( $inCodigoUF             );
            $obCmbUF->setNull               ( true                    );
            $obCmbUF->setStyle              ( "width: 220px"          );
            $obCmbUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio')" );

            $obTxtCodMunicipio = new TextBox;
            $obTxtCodMunicipio->setRotulo    ( "*Munic&iacute;pio"  );
            $obTxtCodMunicipio->setName      ( "inCodigoMunicipio" );
            $obTxtCodMunicipio->setValue     ( $inCodigoMunicipio  );
            $obTxtCodMunicipio->setSize      ( 8                   );
            $obTxtCodMunicipio->setMaxLength ( 8                   );
            $obTxtCodMunicipio->setNull      ( true                );

            $obCmbMunicipio = new Select;
            $obCmbMunicipio->setName       ( "inCodMunicipio"   );
            $obCmbMunicipio->addOption     ( "", "Selecione"    );
            $obCmbMunicipio->setCampoId    ( "cod_municipio"    );
            $obCmbMunicipio->setCampoDesc  ( "nom_municipio"    );
            $obCmbMunicipio->setValue      ( $inCodigoMunicipio );
            $obCmbMunicipio->preencheCombo ( $rsMunicipios      );
            $obCmbMunicipio->setNull       ( true               );
            $obCmbMunicipio->setStyle      ( "width: 220px"     );

            $obBscCGM = new BuscaInner;
            $obBscCGM->setRotulo         ( "*CGM do Prestador" );
            $obBscCGM->setId             ( "stCGM" );
            $obBscCGM->setNull ( truee );
            $obBscCGM->obCampoCod->setName       ( "inCGM" );
            $obBscCGM->obCampoCod->obEvento->setOnChange( "buscaValor('PreencheCGM');" );
            $obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stCGM','','".Sessao::getId()."','800','450');" );

            $obMontaServico = new MontaServico;
            $obMontaServico->setCadastroServico( false );
            $obMontaServico->setRotulo( "*Serviço" );

            $obTxtValorRetido = new Numerico;
            $obTxtValorRetido->setRotulo ( "*Valor Retido" );
            $obTxtValorRetido->setName ( "flValorRetido" );
            $obTxtValorRetido->setId ( "flValorRetido" );
            $obTxtValorRetido->setDecimais ( 2 );
            $obTxtValorRetido->setMaxValue ( 99999999999999.99 );
            $obTxtValorRetido->setNull ( true );
            $obTxtValorRetido->setNegativo ( false );
            $obTxtValorRetido->setNaoZero ( true );
            $obTxtValorRetido->setSize ( 20 );
            $obTxtValorRetido->setMaxLength ( 20 );

            $obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF );
            $obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
            $obFormulario->addComponente ( $obBscCGM );
            $obMontaServico->geraFormulario ( $obFormulario );
            $obFormulario->addComponente($obTxtValorRetido);

            $obFormulario->montaInnerHTML();

            $stJs = "$('spnServico').innerHTML = '".$obFormulario->getHTML()."';";
        }

        return $stJs;
    }

    public function recuperarListaProcessoFiscalEconomica($stFiltro)
    {
        return $this->controller->getListaProcessoFiscalEconomica($stFiltro);
    }

    public function recuperarListaProcessoFiscalEconomicaDocumentos($stFiltro)
    {
        return $this->controller->getListaProcessoFiscalEconomicaDocumentos($stFiltro);
    }

    public function executarCheckBox($lista)
    {
        $count = count($lista->arElementos);
        for ($i = 0; $i < $count; $i++) {
            foreach ($lista->arElementos[$i] as $ch => $vlr) {
                if ($ch == 'cod_documento_entrega') {
                    $checkBox = new CheckBox();
                    if ($vlr != "") {
                        $checkBox->setChecked   ( true );
                    } else {
                        $checkBox->setChecked   ( false );
                    }
                    $checkBox->setDisabled  ( true );
                    $checkBox->montaHtml();
                    $lista->arElementos[$i]['check'] = $checkBox->getHtml();
                }
            }
        }

        return $lista;
    }

    public function filtrosProcessoFiscal($param)
    {
        if ($param['inTipoFiscalizacao'] != "") {
                $stFiltro[] = " pf.cod_tipo = " .$param['inTipoFiscalizacao']. "\n";
        }

        if ($param['inCodProcesso'] != "") {
            $stFiltro[] = " pf.cod_processo = " .$param['inCodProcesso']. "\n";
        }

        if ($param['inInscricaoEconomica'] != "") {
            $stFiltro[] = " pfe.inscricao_economica = " .$param['inInscricaoEconomica']. "\n";
        }

        if ($param['inCodImovel'] != "") {
            $stFiltro[] = " pfo.inscricao_municipal = " .$param['inCodImovel']. "\n";
        }

        if ($param['numcgm'] != "") {
            $stFiltro[] = " fc.numcgm = " .$param['numcgm']. "\n";
        }

        if ($param['inCodFiscal'] != "") {
            $stFiltro[] = " fc.cod_fiscal = " .$param['inCodFiscal']. "\n";
        }

        if ($param['boInicio']) {
            $stFiltro[] = " fif.cod_processo notnull \n";
            $stFiltro[] = " pfc.cod_processo is null \n";
            $stFiltro[] = " ftf.cod_processo is null \n";
        }

        $return = " ";

        if ($stFiltro) {
            foreach ($stFiltro as $chave => $valor) {
                if ($chave == 0) {
                    $return .= $valor;
                } else {
                    $return .= " AND ".$valor;
                }
            }
        }

        return $return;
    }
}
