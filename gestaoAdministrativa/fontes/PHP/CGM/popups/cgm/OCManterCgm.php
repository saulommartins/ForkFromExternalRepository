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
    * Página de Oculto
    * Data de Criação: 02/04/2009

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Id:$
*/

$stCtrl = $_REQUEST['stCtrl'];

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php"  );
include_once(CAM_GA_ADM_COMPONENTES."ISelectEscolaridade.class.php");
include_once(CAM_GA_ADM_COMPONENTES."ISelectNacionalidade.class.php");
include_once(CAM_GA_ADM_COMPONENTES."ISelectPais.class.php");
include_once(CAM_GA_ADM_COMPONENTES."ISelectUF.class.php");
include_once(CAM_GP_FRO_COMPONENTES."ISelectCategoriaHabilitacao.class.php");
include_once(CAM_GRH_PES_COMPONENTES."ITextBoxPISPASEP.class.php");

function montaDadosPorTipo()
{
    $stTipo = $_REQUEST['boPessoa'];
    $obFormulario   = new Formulario;
    $boNull = ( $_REQUEST['boInterno'] );

    if ($stTipo=='fisica') {
        $obTxtNome = new TextBox;
        $obTxtNome->setRotulo   ('Nome');
        $obTxtNome->setTitle    ('Informe o nome');
        $obTxtNome->setName     ('stNome');
        $obTxtNome->setId       ('stNome');
        $obTxtNome->setSize     (50);
        $obTxtNome->setMaxLength(200);
        $obTxtNome->setNull     ( false );

        $obTxtCPF = new CPF;
        $obTxtCPF->setRotulo   ('CPF');
        $obTxtCPF->setTitle    ('Informe o CPF');
        $obTxtCPF->setName     ('stCPF');
        $obTxtCPF->setId       ('stCPF');
        $obTxtCPF->setNull     ( $boNull );

        $obTxtRG = new TextBox;
        $obTxtRG->setRotulo   ('RG');
        $obTxtRG->setTitle    ('Informe o documento de identidade');
        $obTxtRG->setName     ('stRG');
        $obTxtRG->setId       ('stRG');
        $obTxtRG->setSize     (16);
        $obTxtRG->setMaxLength(15);
        $obTxtRG->setNull     ( $boNull );

        $obTxtOrgaoEmissor = new TextBox;
        $obTxtOrgaoEmissor->setRotulo   ('Órgão emissor');
        $obTxtOrgaoEmissor->setTitle    ('Informe o órgão emissor');
        $obTxtOrgaoEmissor->setName     ('stOrgaoEmissor');
        $obTxtOrgaoEmissor->setId       ('stOrgaoEmissor');
        $obTxtOrgaoEmissor->setSize     (10);
        $obTxtOrgaoEmissor->setMaxLength(20);
        $obTxtOrgaoEmissor->setNull     ( $boNull );

        $obSelectUF = new ISelectUF;
        $obSelectUF->setRotulo ('Órgão emissor');
        $obSelectUF->setNull     ( $boNull );

        $obTxtDataEmissao = new Data;
        $obTxtDataEmissao->setRotulo   ('Data da emissão');
        $obTxtDataEmissao->setTitle    ('Informe a data da emissão');
        $obTxtDataEmissao->setName     ('stDataEmissao');
        $obTxtDataEmissao->setId       ('stDataEmissao');

        $obTxtCNH = new Inteiro;
        $obTxtCNH->setRotulo   ('CNH');
        $obTxtCNH->setTitle    ('Informe o número da carteira nacional de habilitação');
        $obTxtCNH->setName     ('stCNH');
        $obTxtCNH->setId       ('stCNH');
        $obTxtCNH->setSize     (15);
        $obTxtCNH->setMaxLength(15);

        $obSelectCategoriaHabilitacao = new ISelectCategoriaHabilitacao;
        $obSelectCategoriaHabilitacao->setNull     ( false );
        $obSelectCategoriaHabilitacao->setValue    ( 0 );

        $obTxtDataValidade = new Data;
        $obTxtDataValidade->setRotulo   ('Data de validade da CNH');
        $obTxtDataValidade->setTitle    ('Informe a data de validade da CNH' );
        $obTxtDataValidade->setName     ('stDataValidade');
        $obTxtDataValidade->setId       ('stDataValidade');

        $obTxtPISPASEP = new ITextBoxPISPASEP;

        $obSelectNacionalidade = new ISelectNacionalidade;
        $obSelectNacionalidade->setNull     ( false );

        $obSelectEscolaridade = new ISelectEscolaridade;
        $obSelectEscolaridade->setNull     ( $boNull );

        $obTxtDataNascimento = new Data;
        $obTxtDataNascimento->setRotulo   ('Data de nascimento');
        $obTxtDataNascimento->setTitle    ('Informe a data de nascimento');
        $obTxtDataNascimento->setName     ('stDataNascimento');
        $obTxtDataNascimento->setId       ('stDataNascimento');

        $obRdbMasculino = new Radio;
        $obRdbMasculino->setRotulo ("Sexo");
        $obRdbMasculino->setTitle  ("Selecione o sexo");
        $obRdbMasculino->setName   ("stSexo");
        $obRdbMasculino->setLabel  ("Masculino");
        $obRdbMasculino->setValue  ("M");
        $obRdbMasculino->setChecked( true );
        $obRdbMasculino->setNull     ( false );
        $obRdbFeminino = new Radio;
        $obRdbFeminino->setName   ("stSexo");
        $obRdbFeminino->setLabel  ("Feminino");
        $obRdbFeminino->setValue  ("F");
        $obRdbFeminino->setChecked( false );

        $obFormulario->addComponente    ($obTxtNome);
        $obFormulario->addComponente    ($obTxtCPF);
        $obFormulario->addComponente    ($obTxtRG);
        $obFormulario->agrupaComponentes( array( $obTxtOrgaoEmissor, $obSelectUF ) );
        $obFormulario->addComponente    ($obTxtDataEmissao);
        $obFormulario->addComponente    ($obTxtCNH);
        $obFormulario->addComponente    ($obSelectCategoriaHabilitacao);
        $obFormulario->addComponente    ($obTxtDataValidade);
        $obFormulario->addComponente    ($obTxtPISPASEP);
        $obFormulario->addComponente    ($obSelectNacionalidade);
        $obFormulario->addComponente    ($obSelectEscolaridade);
        $obFormulario->addComponente    ($obTxtDataNascimento);
        $obFormulario->agrupaComponentes( array($obRdbMasculino, $obRdbFeminino) );

    } else {
        $obTxtRazao = new TextBox;
        $obTxtRazao->setRotulo   ('Razão social');
        $obTxtRazao->setTitle    ('Informe a razão social do CGM');
        $obTxtRazao->setName     ('stRazaoSocial');
        $obTxtRazao->setId       ('stRazaoSocial');
        $obTxtRazao->setSize     (50);
        $obTxtRazao->setMaxLength(200);
        $obTxtRazao->setNull     ( false );

        $obTxtNomeFantasia = new TextBox;
        $obTxtNomeFantasia->setRotulo   ('Nome fantasia');
        $obTxtNomeFantasia->setTitle    ('Informe o nome fantasia da empresa');
        $obTxtNomeFantasia->setName     ('stNomeFantasia');
        $obTxtNomeFantasia->setId       ('stNomeFantasia');
        $obTxtNomeFantasia->setSize     (50);
        $obTxtNomeFantasia->setMaxLength(200);
        $obTxtNomeFantasia->setNull     ( $boNull );

        $obTxtCNPJ = new CNPJ;
        $obTxtCNPJ->setRotulo   ('CNPJ');
        $obTxtCNPJ->setTitle    ('Informe o CNPJ');
        $obTxtCNPJ->setName     ('stCNPJ');
        $obTxtCNPJ->setId       ('stCNPJ');
        $obTxtCNPJ->setNull     ( $boNull );

        $obTxtInscricaoEstadual = new Inteiro;
        $obTxtInscricaoEstadual->setRotulo   ('Inscrição estadual');
        $obTxtInscricaoEstadual->setTitle    ('Informe a inscrição estadual');
        $obTxtInscricaoEstadual->setName     ('stInscricaoEstadual');
        $obTxtInscricaoEstadual->setId       ('stInscricaoEstadual');
        $obTxtInscricaoEstadual->setSize     (14);
        $obTxtInscricaoEstadual->setMaxLength(14);

        $obFormulario->addComponente    ($obTxtRazao);
        $obFormulario->addComponente    ($obTxtNomeFantasia);
        $obFormulario->addComponente    ($obTxtCNPJ);
        $obFormulario->addComponente    ($obTxtInscricaoEstadual);
    }

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    $js = "";
    $js.= " f.stEval.value = '$stEval'; \n";
    $js.= ' d.getElementById(\'spnDadosCgm\').innerHTML = \''.$obFormulario->getHtml() .'\';';

    return $js;

}

switch ($stCtrl) {
    case "montaDadosPorTipo":
        $js = montaDadosPorTipo();
    break;
    case "montaLogradouro":
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php" );
        $obRCIMTrecho       = new RCIMTrecho;
        $rsLogradouro       = new RecordSet;

        $stCor = $_REQUEST['stCor'];
        $campoInnerLogr     = "campoInnerLogr".$stCor;
        $inNumLogradouro    = "inNumLogradouro".$stCor;
        $inCodigoBairro     = "inCodigoBairro".$stCor;
        $inCodMunicipio     = "inCodMunicipio".$stCor;
        $stMunicipio        = "stMunicipio".$stCor;
        $inCodEstado        = "inCodEstado".$stCor;
        $stEstado           = "stEstado".$stCor;
        $cmbBairro          = "cmbBairro".$stCor;
        $cmbCEP             = "cmbCEP".$stCor;
        $pais               = "pais".$stCor;

        if ( empty( $_REQUEST["$inNumLogradouro"] ) || empty( $_REQUEST["$pais"] ) ) {
            $stJs .= 'd.getElementById("'.$campoInnerLogr.'").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("'.$stMunicipio.'").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("'.$stEstado.'").innerHTML = "&nbsp;";';
            $stJs .= 'd.getElementById("'.$inCodMunicipio.'").value = "";';
            $stJs .= 'd.getElementById("'.$inCodEstado.'").value = "";';
            $stJs .= "limpaSelect(f.$cmbBairro,0); \n\r";
            $stJs .= "limpaSelect(f.$cmbCEP,0); \n\r";
            $stJs .= "f.".$cmbBairro."[0] = new Option('Selecione','', 'selected');\n\r";
            $stJs .= "f.".$cmbCEP."[0] = new Option('Selecione','', 'selected');\n\r";
            $stJs .= "f.$inNumLogradouro.value = \"\";";
        } else {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["$inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro, "", $_REQUEST["$pais"] );
            if ( $rsLogradouro->eof() ) {
                $stJs .= 'f.'.$inNumLogradouro.'.value = "";';
                $stJs .= 'f.'.$inNumLogradouro.'.focus();';
                $stJs .= 'd.getElementById("'.$campoInnerLogr.'").innerHTML = "&nbsp;";';
                $stJs .= 'd.getElementById("'.$stMunicipio.'").innerHTML = "&nbsp;";';
                $stJs .= 'd.getElementById("'.$stEstado.'").innerHTML = "&nbsp;";';
                $stJs .= 'd.getElementById("'.$inCodMunicipio.'").value = "";';
                $stJs .= 'd.getElementById("'.$inCodEstado.'").value = "";';

                $stJs .= "limpaSelect(f.".$cmbBairro.",0); \n\r";
                $stJs .= "limpaSelect(f.".$cmbCEP.",0); \n\r";
                $stJs .= "f.".$cmbBairro."[0] = new Option('Selecione','', 'selected');\n\r";
                $stJs .= "f.".$cmbCEP."[0] = new Option('Selecione','', 'selected');\n\r";

                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["$inNumLogradouro"].")', 'form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
                $stJs .= 'd.getElementById("'.$campoInnerLogr.'").innerHTML = "'.$stNomeLogradouro.'";';

                $stNomeMunicipio = $rsLogradouro->getCampo ("cod_municipio") . ' - ' .$rsLogradouro->getCampo ("nom_municipio");
                $stNomeEstado = $rsLogradouro->getCampo ("cod_uf") . ' - ' .$rsLogradouro->getCampo ("nom_uf");

                //$stJs .= "f.stNomeLogradouro.value = '". $stNomeLogradouro."';";
                $stJs .= 'd.getElementById("'.$stMunicipio.'").innerHTML = "'.$stNomeMunicipio.'";';
                $stJs .= 'd.getElementById("'.$stEstado.'").innerHTML = "'.$stNomeEstado.'";';
                $stJs .= 'd.getElementById("'.$inCodMunicipio.'").value = "'. $rsLogradouro->getCampo ("cod_municipio") .'";';
                $stJs .= 'd.getElementById("'.$inCodEstado.'").value = "'. $rsLogradouro->getCampo ("cod_uf") .'";';
                //$stJs .= 'f.inCodMunicipio.value = "'.$rsLogradouro->getCampo ("cod_municipio").'";';
                //$stJs .= 'f.inCodUF.value = "'.$rsLogradouro->getCampo ("cod_uf").'";';

                $obRCIMTrecho->setCodigoLogradouro( $rsLogradouro->getCampo ("cod_logradouro") );
                $obRCIMTrecho->listarBairroLogradouro( $rsBairro );
                $obRCIMTrecho->listarCEP( $rsCep );

                $stJs2 .= "limpaSelect(f.".$cmbBairro.",0); \n\r";
                $stJs2 .= "limpaSelect(f.".$cmbCEP.",0); \n\r";
                $stJs2 .= "f.".$cmbBairro."[0] = new Option('Selecione','', 'selected');\n\r";
                $stJs2 .= "f.".$cmbCEP."[0] = new Option('Selecione','', 'selected');\n\r";
                /* bairro ****************/
                $inContador = 1;
                while ( !$rsBairro->eof() ) {

                    $inCodBairroTMP  = $rsBairro->getCampo( "cod_bairro" );
                    $stNomeBairroTMP = $rsBairro->getCampo( "nom_bairro" );

                    $stJs2 .= "f.".$cmbBairro.".options[$inContador] = new Option('".$stNomeBairroTMP."','".$inCodBairroTMP."'); \n\r";
                    $inContador++;
                    $rsBairro->proximo();

                }
                $stJs .= 'f.'.$inCodigoBairro.'.value = "";';

                /* cep *******************/
                $inContador = 1;
                while ( !$rsCep->eof() ) {
                    $stCep = $rsCep->getCampo( "cep" );
                    $stJs2 .= "f.".$cmbCEP.".options[$inContador] = new Option('".$stCep."','".$stCep."'); \n";
                    $inContador++;
                    $rsCep->proximo();
                }

                //executaFrameOcultoParent($stJs2);
            }
        }
        //executaFrameOcultoParent($stJs);
        //executaFrameOcultoParent($stJs.$stJs2);
        $js = $stJs.$stJs2;
    break;
}

if ($js) {
    executaFrameOcultoParent($js);
    //echo $js;
}

function executaFrameOcultoParent($stJs)
{
    print '<html>
           <head>
           <script src="'.CAM_GA.'javaScript/ifuncoesJs.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/funcoesJs.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/genericas.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/Window.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/mascaras.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/tipo.js" type="text/javascript"></script>
           <script src="'.CAM_GA.'javaScript/arvore.js" type="text/javascript"></script>
           <script type="text/javascript">
           function executa()
           {
                var mensagem = "";
                var erro = false;
                var f =   parent.document.frm;
                var d =   parent.document;
                var jq_ = parent.document.jQuery;
                var aux;
                '.$stJs.'

                if (erro) alertaAviso(mensagem,"form","erro","'.Sessao::getId().'");
           }
           </script>
           </head>
           <body onLoad="javascript:executa();">
           </body>
           </html>';
}

?>
