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
    * Página de Frame Oculto de Autoridade
    * Data de Criação   : 14/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCManterAutoridade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.08
*/

/*
$Log$
Revision 1.3  2007/03/01 14:01:43  cercato
Bug #8533#

Revision 1.2  2006/09/26 11:14:34  dibueno
adição da função para busca de autoridade

Revision 1.1  2006/09/18 17:18:29  cercato
formularios da autoridade de acordo com interface abstrata.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php" );

switch ($_REQUEST['stCtrl']) {
    case "preencheOAB":
        $obFormulario = new Formulario;

        //OAB
        $obTxtOAB = new Inteiro;
        $obTxtOAB->setRotulo ( "OAB" );
        $obTxtOAB->setTitle ( "Informe o número de registro na OAB do procurador municipal." );
        $obTxtOAB->setName ( "stOAB" );
        $obTxtOAB->setNull ( false );
        $obTxtOAB->setValue ( $_REQUEST["stOAB"] );

        $obITextBoxSelectUF = new TextBoxSelect;
        $obTMapeamento          = new TUF();
        $rsRecordSet            = new Recordset;

        $obTMapeamento->recuperaTodos($rsRecordSet,'',' ORDER BY cod_uf');

        $obITextBoxSelectUF->setRotulo              ( "UF"                );
        $obITextBoxSelectUF->setName                ( "inCodUF"                );
        $obITextBoxSelectUF->setTitle               ( "Selecione o UF."    );

        $obITextBoxSelectUF->obTextBox->setRotulo              ( "UF"                );
        $obITextBoxSelectUF->obTextBox->setTitle               ( "Selecione o UF."    );
        $obITextBoxSelectUF->obTextBox->setName                ( "inCodUFTxt"        );
        $obITextBoxSelectUF->obTextBox->setId                  ( "inCodUFTxt"        );
        $obITextBoxSelectUF->obTextBox->setSize                ( 12                     );
        $obITextBoxSelectUF->obTextBox->setMaxLength           ( 10                      );
        $obITextBoxSelectUF->obTextBox->setInteiro             ( true                   );
        $obITextBoxSelectUF->obTextBox->setValue               ( $_REQUEST["inCodUF"] );

        $obITextBoxSelectUF->obSelect->setRotulo              ( "Banco"                         );
        $obITextBoxSelectUF->obSelect->setName                ( "inCodUF"                    );
        $obITextBoxSelectUF->obSelect->setId                  ( "inCodUF"                    );
        $obITextBoxSelectUF->obSelect->setCampoID             ( "cod_uf"                     );
        $obITextBoxSelectUF->obSelect->setCampoDesc           ( "nom_uf"                     );
        $obITextBoxSelectUF->obSelect->addOption              ( "", "Selecione"                 );
        $obITextBoxSelectUF->obSelect->preencheCombo          ( $rsRecordSet                    );
        $obITextBoxSelectUF->obSelect->setStyle               ( "width: 200px"                );
        $obITextBoxSelectUF->obSelect->setValue               ( $_REQUEST["inCodUF"] );

        $obFormulario->addComponente ( $obTxtOAB );
        $obFormulario->addComponente ( $obITextBoxSelectUF );
        $obFormulario->montaInnerHTML();

        $js = "d.getElementById('spnAutoridade').innerHTML = '". $obFormulario->getHTML(). "';\n";
        echo $js;
        break;

    case "preencheMatricula":
        $rsListaMatricula = Sessao::read("lstMatricula");
        while ( !$rsListaMatricula->Eof() ) {
            if ( $rsListaMatricula->getCampo("registro") == $_REQUEST[ "inMatricula" ] ) {
                $stJs = " d.getElementById('stInfo').innerHTML = '".$rsListaMatricula->getCampo("descricao")." - ".$rsListaMatricula->getCampo("vigencia")."'; \n";
                echo $stJs;
                break;
            }

            $rsListaMatricula->proximo();
        }
        break;

    case "buscaAutoridade":

        if ($_REQUEST['inCodAutoridade']) {
            include_once ( CAM_GT_DAT_MAPEAMENTO."TDATAutoridade.class.php" );
            $obTDATAutoridade = new TDATAutoridade;
            $stFiltro = " WHERE da.cod_autoridade = ". $_REQUEST['inCodAutoridade']." \n";
            $obTDATAutoridade->recuperaListaAutoridade( $rsAutoridade, $stFiltro, " ORDER BY ps.numcgm " );

            $stDescricao = $rsAutoridade->getCampo('nom_cgm');
            $stJs = "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_REQUEST['stIdCampoDesc']."', 'frm', '".$stDescricao."');";

        } else {
            $js  = 'f.'.$_GET["stNomCampoCod"].'.value = "";';
            $js .= "d.getElementById('".$_REQUEST['stIdCampoDesc']."').innerHTML = '&nbsp;';\n";
            sistemaLegado::executaFrameOculto( $js );
        }

        if ($stJs) echo $stJs;
        exit;

        break;

    case "tipoAutoridade":
        if ($_REQUEST["stTipoAutoridade"] == "procurador") {
            $obFormulario = new Formulario;

            //OAB
            $obTxtOAB = new Inteiro;
            $obTxtOAB->setRotulo ( "OAB" );
            $obTxtOAB->setTitle ( "Informe o número de registro na OAB do procurador municipal." );
            $obTxtOAB->setName ( "stOAB" );
            $obTxtOAB->setNull ( false );

            $obITextBoxSelectUF = new TextBoxSelect;
            $obTMapeamento          = new TUF();
            $rsRecordSet            = new Recordset;

            $obTMapeamento->recuperaTodos($rsRecordSet,'',' ORDER BY cod_uf');

            $obITextBoxSelectUF->setRotulo              ( "UF"                );
            $obITextBoxSelectUF->setName                ( "inCodUF"                );
            $obITextBoxSelectUF->setTitle               ( "Selecione o UF."    );

            $obITextBoxSelectUF->obTextBox->setRotulo              ( "UF"                );
            $obITextBoxSelectUF->obTextBox->setTitle               ( "Selecione o UF."    );
            $obITextBoxSelectUF->obTextBox->setName                ( "inCodUFTxt"        );
            $obITextBoxSelectUF->obTextBox->setId                  ( "inCodUFTxt"        );
            $obITextBoxSelectUF->obTextBox->setSize                ( 12                     );
            $obITextBoxSelectUF->obTextBox->setMaxLength           ( 10                      );
            $obITextBoxSelectUF->obTextBox->setInteiro             ( true                   );

            $obITextBoxSelectUF->obSelect->setRotulo              ( "Banco"                         );
            $obITextBoxSelectUF->obSelect->setName                ( "inCodUF"                    );
            $obITextBoxSelectUF->obSelect->setId                  ( "inCodUF"                    );
            $obITextBoxSelectUF->obSelect->setCampoID             ( "cod_uf"                     );
            $obITextBoxSelectUF->obSelect->setCampoDesc           ( "nom_uf"                     );
            $obITextBoxSelectUF->obSelect->addOption              ( "", "Selecione"                 );
            $obITextBoxSelectUF->obSelect->preencheCombo          ( $rsRecordSet                    );
            $obITextBoxSelectUF->obSelect->setStyle               ( "width: 200px"                );

            $obFormulario->addComponente ( $obTxtOAB );
            $obFormulario->addComponente ( $obITextBoxSelectUF );
            $obFormulario->montaInnerHTML();

            $js = "d.getElementById('spnAutoridade').innerHTML = '". $obFormulario->getHTML(). "';\n";
        } else {
            $js = "d.getElementById('spnAutoridade').innerHTML = '&nbsp;';\n";
        }

        echo $js;
        break;
}
