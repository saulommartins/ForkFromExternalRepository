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
    * Página de Frame Oculto para Consulta de Transferencia de Imóveis
    * Data de Criação   : 16/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    * $Id: OCConsultaImovelTransferencia.php 63503 2015-09-03 18:25:17Z jean $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.5  2006/10/27 17:53:42  dibueno
Alterações nos ALIAS do PRocesso e CGM.
Alteração para setar boEfetivacao = false

Revision 1.4  2006/09/18 10:30:20  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO . "RCIMTransferencia.class.php" );
include_once( CAM_GT_CIM_NEGOCIO . "RCIMImovel.class.php"        );
include_once( CAM_GT_CIM_NEGOCIO . "RCIMCorretagem.class.php"    );
include_once( CAM_GA_PROT_NEGOCIO. "RProcesso.class.php"         );
include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php"              );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRCIMTransferencia = new RCIMTransferencia;
$obRCIMImovel        = new RCIMImovel (new RCIMLote);
$obRCIMProprietario  = new RCIMProprietario ( $obRCIMImovel );
$obRProcesso         = new RProcesso;
$obRCGM              = new RCGM;
$rsCGM               = new Recordset;

function listaAdquirentes($rsRecordSet, $boExecuta = true)
{
    if ( $rsRecordSet->getNumLinhas() > 0 ) {
        $rsRecordSet->addFormatacao( "quota_ant" , "NUMERIC_BR" );
        $rsRecordSet->addFormatacao( "quota"     , "NUMERIC_BR" );
        $obLista = new Lista;
        $obLista->setMostraPaginacao           ( false                   );
        $obLista->setTitulo                    ( "Lista de adquirentes"  );
        $obLista->setRecordSet                 ( $rsRecordSet            );
        $obLista->addCabecalho                 (                         );
        $obLista->ultimoCabecalho->addConteudo ("&nbsp;"                 );
        $obLista->ultimoCabecalho->setWidth    ( 5                       );
        $obLista->commitCabecalho              (                         );
        $obLista->addCabecalho                 (                         );
        $obLista->ultimoCabecalho->addConteudo ( "CGM"                   );
        $obLista->ultimoCabecalho->setClass    ( 'labelleft'             );
        $obLista->ultimoCabecalho->setWidth    ( 10                      );
        $obLista->commitCabecalho              (                         );
        $obLista->addCabecalho                 (                         );
        $obLista->ultimoCabecalho->addConteudo ( "Nome"                  );
        $obLista->ultimoCabecalho->setClass    ( 'labelleft'             );
        $obLista->ultimoCabecalho->setWidth    ( 46                      );
        $obLista->commitCabecalho              (                         );
        $obLista->addCabecalho                 (                         );
        $obLista->ultimoCabecalho->addConteudo ( "Quota Original (%)"    );
        $obLista->ultimoCabecalho->setClass    ( 'labelleft'             );
        $obLista->ultimoCabecalho->setWidth    ( 17                      );
        $obLista->commitCabecalho              (                         );
        $obLista->addCabecalho                 (                         );
        $obLista->ultimoCabecalho->addConteudo ( "Quota Resultante (%)"  );
        $obLista->ultimoCabecalho->setClass    ( 'labelleft'             );
        $obLista->ultimoCabecalho->setWidth    ( 17                      );
        $obLista->commitCabecalho              (                         );

        $obLista->addDado                      (                         );
        $obLista->ultimoDado->setCampo         ( "codigo"                );
        $obLista->commitDado                   (                         );
        $obLista->addDado                      (                         );
        $obLista->ultimoDado->setCampo         ( "nome"                  );
        $obLista->commitDado                   (                         );
        $obLista->addDado                      (                         );
        $obLista->ultimoDado->setCampo         ( "quota_ant"             );
        $obLista->commitDado                   (                         );
        $obLista->addDado                      (                         );
        $obLista->ultimoDado->setCampo         ( "quota"                 );
        $obLista->commitDado                   (                         );

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    $stJs .= "d.getElementById('spnAdquirentes').innerHTML = '".$stHtml."';";
    if ($boExecuta == true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function listaProprietarios($rsRecordset, $boExecuta = true)
{
    $rsRecordset->addFormatacao( "quota" , "NUMERIC_BR" );
    $obLista = new Lista;
    $obLista->setMostraPaginacao           ( false                    );
    $obLista->setTitulo                    ( "Lista de Transmitentes" );
    $obLista->setRecordSet                 ( $rsRecordset             );
    $obLista->addCabecalho                 (                          );
    $obLista->ultimoCabecalho->addConteudo ("&nbsp;"                  );
    $obLista->ultimoCabecalho->setWidth    ( 5                        );
    $obLista->commitCabecalho              (                          );
    $obLista->addCabecalho                 (                          );
    $obLista->ultimoCabecalho->addConteudo ( "CGM"                    );
    $obLista->ultimoCabecalho->setClass    ( 'labelleft'              );
    $obLista->ultimoCabecalho->setWidth    ( 10                       );
    $obLista->commitCabecalho              (                          );
    $obLista->addCabecalho                 (                          );
    $obLista->ultimoCabecalho->addConteudo ( "Nome"                   );
    $obLista->ultimoCabecalho->setClass    ( 'labelleft'              );
    $obLista->ultimoCabecalho->setWidth    ( 63                       );
    $obLista->commitCabecalho              (                          );
    $obLista->addCabecalho                 (                          );
    $obLista->ultimoCabecalho->addConteudo ( "Quota (%)"              );
    $obLista->ultimoCabecalho->setClass    ( 'labelleft'              );
    $obLista->ultimoCabecalho->setWidth    ( 17                       );
    $obLista->commitCabecalho              (                          );
    $obLista->addDado                      (                          );
    $obLista->ultimoDado->setCampo         ( "cgm"                    );
    $obLista->commitDado                   (                          );
    $obLista->addDado                      (                          );
    $obLista->ultimoDado->setCampo         ( "nome"                   );
    $obLista->commitDado                   (                          );
    $obLista->addDado                      (                          );
    $obLista->ultimoDado->setCampo         ( "quota"                  );
    $obLista->commitDado                   (                          );

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnProprietarios').innerHTML = '".$stHtml."';";
    if ($boExecuta == true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

switch ($stCtrl) {
    case "visualizarTransferencia":

        $obRCIMNaturezaTransferencia = new RCIMNaturezaTransferencia;
        $obRCIMTransferencia         = new RCIMTransferencia;
        $obRCIMConfiguracao          = new RCIMConfiguracao;
        $obRCIMImovel                = new RCIMImovel( new RCIMLote );
        $obRCIMProprietario          = new RCIMProprietario ( $obRCIMImovel );

        $obRCIMTransferencia->setCodigoTransferencia( $_REQUEST["cod_transferencia"] );
        $obRCIMTransferencia->setEfetivacao ('f');
        $obRCIMTransferencia->consultarTransferencia();

        $obRCIMNaturezaTransferencia->setCodigoNatureza( $obRCIMTransferencia->getCodigoNatureza() );
        $obRCIMNaturezaTransferencia->consultarNaturezaTransferencia();

        $obLblNatureza = new Label;
        $obLblNatureza->setRotulo    ( "Natureza da Transferência"  );
        $obLblNatureza->setValue     ( $obRCIMNaturezaTransferencia->getDescricaoNatureza());

        $stProcesso = $_REQUEST["cod_processo"]."/".$_REQUEST["exercicio_proc"];
        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo    ( "Processo"                 );
        $obLblProcesso->setValue     ( $stProcesso                );

        $stCreci = $_REQUEST['creci']." - ".$_REQUEST['nomcgm'];
        $obLblCreci = new Label;
        $obLblCreci->setRotulo       ( "CRECI"                    );
        $obLblCreci->setValue        ( $stCreci                   );

        $obLblDtEfetivacao = new Label;
        $obLblDtEfetivacao->setRotulo( "Data de Efetivação"       );
        $obLblDtEfetivacao->setValue ( $_REQUEST['dt_efetivacao'] );

        $obLblObservacao = new Label;
        $obLblObservacao->setRotulo( "Observação"       );
        $obLblObservacao->setValue ( $_REQUEST['stObservacao'] );

        $obFormulario = new Formulario;
        $obFormulario->addTitulo    ( "Dados de transferência" );
        $obFormulario->addComponente( $obLblNatureza     );
        $obFormulario->addComponente( $obLblProcesso     );
        $obFormulario->addComponente( $obLblCreci        );
        $obFormulario->addComponente( $obLblDtEfetivacao );
        $obFormulario->addComponente( $obLblObservacao   );

        /* MONTA LISTA DE Ex-PROPRIETARIOS E ADQUIRENTES  */

        $obRCIMImovel->setNumeroInscricao( $obRCIMTransferencia->getInscricaoMunicipal() );
        $obRCIMProprietario->listarProprietariosPorImovel( $rsProrprietarios );

        include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMExProprietario.class.php" );

        $stFiltro = " WHERE timestamp <= '".$rsProrprietarios->getCampo("timestamp")."' AND inscricao_municipal = ".$obRCIMTransferencia->getInscricaoMunicipal()." ORDER BY timestamp DESC LIMIT 1";
        $obTCIMExProprietario = new TCIMExProprietario;
        $obTCIMExProprietario->recuperaTodos( $rsListaTimestampEx, $stFiltro );
        unset( $obTCIMExProprietario );
        $obRCIMProprietario->setTimestamp( $rsListaTimestampEx->getCampo("timestamp") );
        $obRCIMProprietario->listarExProprietarios( $rsExProprietarios );
        /* Lista de Ex-Proprietarios */

        $arExProprietarios = array();
        $inCont = 0;
        while ( !$rsExProprietarios->eof() ) {
            $inNumCgm   = $rsExProprietarios->getCampo( "numcgm" );
            $flQuota    = $rsExProprietarios->getCampo( "cota"   );
            $obRCGM->setNumCGM( $inNumCgm );
            $obRCGM->consultar( $rsCGM    );
            $arExProprietarios[$inCont]['inSeq'] = $inCont;
            $arExProprietarios[$inCont]['cgm'  ] = $inNumCgm;
            $arExProprietarios[$inCont]['nome' ] = $obRCGM->getNomCGM();
            $arExProprietarios[$inCont]['quota'] = $flQuota;
            $rsExProprietarios->proximo();
            $inCont++;
        }
        $rsExProprietarios = new Recordset;
        $rsExProprietarios->preenche( $arExProprietarios );

        /* Lista de Adquirintes */

        $rsAdquirentes = new Recordset;
        $obRCIMTransferencia->setCodigoTransferencia( $_REQUEST['cod_transferencia'] );
        $obRCIMTransferencia->consultarAdquirentes();
        Sessao::write('Adquirintes', $obRCIMTransferencia->getAdquirentes());
        $rsProrprietarios->setPrimeiroElemento();
        while (!$rsProrprietarios->eof()) {
            $inCont=0;
            $arAdquirintesSessao = Sessao::read('Adquirintes');
            foreach ($arAdquirintesSessao as $inFor) {
                if ($rsExProprietarios->getCampo("numcgm") == $arAdquirintesSessao[$inCont]['codigo']) {
                    $arAdquirintesSessao[$inCont]['quota_ant'] = $rsProrprietarios->getCampo("cota");
                }
                $inCont++;
            }
            $rsProrprietarios->proximo();
        }
        $rsAdquirentes->preenche ( $arAdquirintesSessao );

        /* FIM MONTA LISTA DE Ex-PROPRIETARIOS E ADQUIRENTES */

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $stJs = "d.getElementById('spnTransferencia').innerHTML = '".$stHtml."';";
        $stJs .= listaProprietarios( $rsExProprietarios , false );
        $stJs .= listaAdquirentes  ( $rsAdquirentes   , false );

        SistemaLegado::executaFrameOculto( $stJs );
    break;
}
?>
