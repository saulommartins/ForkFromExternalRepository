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
    * Página de processamento oculto para o cadastro de transferência de proipriedade
    * Data de Criação   : 22/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini
                             Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: OCManterTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.17
*/

/*
$Log$
Revision 1.17  2006/09/18 10:31:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO  . "RCIMTransferencia.class.php" );
include_once( CAM_GT_CIM_NEGOCIO  . "RCIMImovel.class.php"        );
include_once( CAM_GT_CIM_NEGOCIO  . "RCIMCorretagem.class.php"    );
include_once( CAM_GA_PROT_NEGOCIO . "RProcesso.class.php"         );
include_once( CAM_GA_CGM_NEGOCIO  . "RCGM.class.php"              );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRCIMTransferencia = new RCIMTransferencia;
$obRCIMImovel        = new RCIMImovel (new RCIMLote);
$obRCIMProprietario  = new RCIMProprietario ( $obRCIMImovel );
$obRProcesso         = new RProcesso;
$obRCGM              = new RCGM;
$rsCGM               = new Recordset;

function verificaPermissaoUsuario()
{
    ;

    $sSQL  = " SELECT                                              \n";
    $sSQL .= "     DISTINCT a.ordem,                               \n";
    $sSQL .= "     m.cod_modulo,                                   \n";
    $sSQL .= "     m.nom_modulo,                                   \n";
    $sSQL .= "     f.cod_funcionalidade,                           \n";
    $sSQL .= "     f.nom_funcionalidade,                           \n";
    $sSQL .= "     a.nom_acao,                                     \n";
    $sSQL .= "     a.nom_arquivo,                                  \n";
    $sSQL .= "     a.parametro,                                    \n";
    $sSQL .= "     a.complemento_acao,                             \n";
    $sSQL .= "     f.nom_diretorio as func_dir,                    \n";
    $sSQL .= "     m.nom_diretorio as mod_dir,                     \n";
    $sSQL .= "     g.nom_diretorio as gest_dir,                    \n";
    $sSQL .= "     a.cod_acao                                      \n";
    $sSQL .= " FROM                                                \n";
    $sSQL .= "     administracao.gestao as g,                      \n";
    $sSQL .= "     administracao.modulo as m,                      \n";
    $sSQL .= "     administracao.funcionalidade as f,              \n";
    $sSQL .= "     administracao.acao as a,                        \n";
    $sSQL .= "     administracao.permissao as p                    \n";
    $sSQL .= " WHERE                                               \n";
    $sSQL .= "     g.cod_gestao = m.cod_gestao AND                 \n";
    $sSQL .= "     m.cod_modulo = f.cod_modulo AND                 \n";
    $sSQL .= "     f.cod_funcionalidade = a.cod_funcionalidade AND \n";
    $sSQL .= "     a.cod_acao = p.cod_acao AND                     \n";
    $sSQL .= "     a.cod_acao = ". Sessao::read('acao') ." AND     \n";
    $sSQL .= "     p.numcgm=".Sessao::read('numCgm')." AND         \n";
    $sSQL .= "     p.ano_exercicio = '".Sessao::getExercicio()."'  \n";
    $sSQL .= " ORDER by                                            \n";
    $sSQL .= "     a.ordem                                         \n";

    $obConexao = new Conexao;
    $obConexao->executaSql( $rsAcao, $sSQL );

    if ( $rsAcao->getNumLinhas() > 0 ) {
        return true;
    } else {
        return false;
    }

}

function VerificaDocumentacaoEntregue($listaDocumentos)
{
        $contEntregue = 0;
        $cont = 0;
        $tam = count ( $listaDocumentos );
        while ($cont < $tam) {
            if ($listaDocumentos[$cont]['entregue']== 't' && $listaDocumentos[$cont]['obrigatorio'] != 'não') {
                $contEntregue++;
            }
            $cont++;
        }

        if ($contEntregue == $tam) {
            return true;
        } else {
            return false;
        }

}

function montaCheckBoxAvaliacao($monta)
{
    if ($monta) {

        $obFormulario = new Formulario;

        $obCheckSegueAvaliacao = new CheckBox;
        $obCheckSegueAvaliacao->setName        ( "boSegueAvaliacao"   );
        $obCheckSegueAvaliacao->setRotulo       ( " "                               );
        $obCheckSegueAvaliacao->setValue        ( "1"                              );
        $obCheckSegueAvaliacao->setLabel         ( "Seguir para o formulário de Avaliação de Imóvel?" );
        $obCheckSegueAvaliacao->setChecked   ( false                            );
        //$obCheckSegueAvaliacao->obEvento->setOnChange("buscaValor('AtualizaSegueAvaliacao');");

        $obFormulario->addComponente ($obCheckSegueAvaliacao);
        $obFormulario->montaInnerHTML();

        $js .= "d.getElementById('spnCheckAvalia').innerHTML = '". $obFormulario->getHTML(). "';\n";
        //$js .= "d.getElementById('spnCheckAvalia').innerHTML = '".$meuHtml."';\n";
    } else {
        $js .= "d.getElementById('spnCheckAvalia').innerHTML = ' ';\n";
    }

        SistemaLegado::executaFrameOculto($js);

}
function listaDocumentos($rsRecordSet, $boExecuta=true, $boDesativar=true)
{
    $stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
    if ( $rsRecordSet->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao                   ( false                                       );
        $obLista->setTitulo                            ( "Documentos apresentados"                   );
        $obLista->setRecordSet                         ( $rsRecordSet                                );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ("&nbsp;"                                     );
        $obLista->ultimoCabecalho->setWidth            ( 3                                           );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Entregue"                                  );
        $obLista->ultimoCabecalho->setWidth            ( 10                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Documento"                                 );
        $obLista->ultimoCabecalho->setWidth            ( 62                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Obrigatório"                               );
        $obLista->ultimoCabecalho->setWidth            ( 10                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );

        $obChkEntregue = new Checkbox;
        $obChkEntregue->setName                        ( "boEntregue"                                );
        $obChkEntregue->obEvento->setOnChange          ( "buscaValor('atualizaCheckDocumento');"     );

        $obLista->addDadoComponente                    ( $obChkEntregue                              );
        $obLista->ultimoDado->setCampo                 ( "entregue"                                  );
        $obLista->commitDadoComponente                 (                                             );
        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "nome"                                      );
        $obLista->commitDado                           (                                             );
        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "obrigatorio"                               );
        $obLista->commitDado                           (                                             );

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnDocumentosNatureza').innerHTML = '".$stHtml."';";

    while ( !$rsRecordSet->eof() ) {
           $stJs .= "d.frm.boEntregue_".$rsRecordSet->getCorrente().".checked = ".( $rsRecordSet->getCampo( "entregue" ) == 'f' ? 'false' : 'true' ).";";

           if ( $rsRecordSet->getCampo( "entregue" ) == 't' ) {
               //$stJs .= "d.frm.boEntregue_".$rsRecordSet->getCorrente().".value = 't';";
               //$stJs .= "alert( d.frm.boEntregue_".$rsRecordSet->getCorrente().".value);";
           }

           if ($boDesativar) {
                if ($stAcao == "efetivar" OR $stAcao == "cancelar") {
                    $stJs .= "d.frm.boEntregue_".$rsRecordSet->getCorrente().".disabled = ".( $stAcao == "efetivar" ? ( $rsRecordSet->getCampo( "entregue" ) == 'f' ? 'false' : 'true' ) : 'true' ).";";
                }
           }

           $rsRecordSet->proximo();
    }

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function listaAdquirentes($rsRecordSet, $boExecuta=true)
{
    $stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

    if ( $rsRecordSet->getNumLinhas() > 0 ) {

        $obLista = new Lista;
        $obLista->setMostraPaginacao                   ( false                                       );
        $obLista->setTitulo                            ( "Lista de adquirentes"                      );
        $obLista->setRecordSet                         ( $rsRecordSet                                );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ("&nbsp;"                                     );
        $obLista->ultimoCabecalho->setWidth            ( 3                                           );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "CGM"                                       );
        $obLista->ultimoCabecalho->setWidth            ( 10                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Nome"                                      );
        $obLista->ultimoCabecalho->setWidth            ( 50                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );
        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Quota Atual (%)"                           );
        $obLista->ultimoCabecalho->setWidth            ( 15                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );

        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Quota Futura (%)"                          );
        $obLista->ultimoCabecalho->setWidth            ( 15                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );

        if ($stAcao == "incluir" or $stAcao == "alterar") {
            $obLista->addCabecalho                     (                                             );
            $obLista->ultimoCabecalho->addConteudo     ("&nbsp;"                                     );
            $obLista->ultimoCabecalho->setWidth        ( 3                                           );
            $obLista->commitCabecalho                  (                                             );
        }
        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "codigo"                                    );
        $obLista->commitDado                           (                                             );
        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "nome"                                      );
        $obLista->commitDado                           (                                             );
        /* Quota Atual */

        $obLista->addDado                              (                                              );
        $obLista->ultimoDado->setCampo                 ( "quota_ant"                                  );
        $obLista->commitDado                           (                                              );
        /* Quota Futura */

        $obLista->addDado                              (                                              );
        $obLista->ultimoDado->setCampo                 ( "quota"                                      );
        $obLista->commitDado                           (                                              );
        if ($stAcao == "incluir" or $stAcao == "alterar") {
            $obLista->addAcao                              (                                              );
            $obLista->ultimaAcao->setAcao                  ( "EXCLUIR"                                    );
            $obLista->ultimaAcao->setFuncao                ( true                                         );
            $obLista->ultimaAcao->setLink                  ( "javascript:excluiDado('excluiAdquirente');" );
            $obLista->ultimaAcao->addCampo                 ( "1","inId"                                   );
            $obLista->commitAcao                           (                                              );
        }

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnAdquirentes').innerHTML = '".$stHtml."';";
    $stJs .= "d.frm.inNumCGM.value                         = '';";
    $stJs .= "d.getElementById('campoInner').innerHTML     = '&nbsp;';";
    $stJs .= "d.frm.nuQuota.value                          = '';";
    //$stJs .= "d.frm.Ok.disabled                            = false;";

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

/* Lista Proprietarios */

function ListaProprietarios($rsRecordset, $boExecuta=true)
{
                    $obLista = new Lista;
                    $obLista->setMostraPaginacao                   ( false                                       );
                    $obLista->setTitulo                            ( "Lista de Proprietários"                    );
                    $obLista->setRecordSet                         ( $rsRecordset                                );
                    $obLista->addCabecalho                         (                                             );
                    $obLista->ultimoCabecalho->addConteudo         ("&nbsp;"                                     );
                    $obLista->ultimoCabecalho->setWidth            ( 3                                           );
                    $obLista->commitCabecalho                      (                                             );
                    $obLista->addCabecalho                         (                                             );
                    $obLista->ultimoCabecalho->addConteudo         ( "CGM"                                       );
                    $obLista->ultimoCabecalho->setWidth            ( 10                                          );
                    $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
                    $obLista->commitCabecalho                      (                                             );
                    $obLista->addCabecalho                         (                                             );
                    $obLista->ultimoCabecalho->addConteudo         ( "Nome"                                      );
                    $obLista->ultimoCabecalho->setWidth            ( 64                                          );
                    $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
                    $obLista->commitCabecalho                      (                                             );
                    $obLista->addCabecalho                         (                                             );
                    $obLista->ultimoCabecalho->addConteudo         ( "Quota Atual(%)"                            );
                    $obLista->ultimoCabecalho->setWidth            ( 10                                          );
                    $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
                    $obLista->commitCabecalho                      (                                             );
                    $obLista->addDado                              (                                             );
                    $obLista->ultimoDado->setCampo                 ( "cgm"                                       );
                    $obLista->commitDado                           (                                             );
                    $obLista->addDado                              (                                             );
                    $obLista->ultimoDado->setCampo                 ( "nome"                                      );
                    $obLista->commitDado                           (                                             );
                    $obLista->addDado                              (                                             );
                    $obLista->ultimoDado->setCampo                 ( "quota"                                     );
                    $obLista->commitDado                           (                                             );

                    $obLista->montaHTML();
                    $stHtml = $obLista->getHTML();
                    $stHtml = str_replace("\n","",$stHtml);
                    $stHtml = str_replace("  ","",$stHtml);
                    $stHtml = str_replace("'","\\'",$stHtml);

                    // preenche a lista com innerHTML
                    $stJs .= "d.getElementById('spnProprietarios').innerHTML = '".$stHtml."';";
                    $arAdquirentesSessao = array();
                    Sessao::write('Adquirentes', $arAdquirentesSessao);

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }

}
/************ Fim - Lista de Proprietarios *************/

//echo '#####ACAO: '. $stCtrl.'<br>';
// Acoes por pagina
switch ($stCtrl) {
    case "MontaAdquirente":
        $inCountAdq = 0;
        $arListaAdquirentes = array();
        $inTotalQuota = 0;
        $arAdquirentesSessao = Sessao::read('Adquirentes');
        if ( count( $arAdquirentesSessao ) > 0  ) {

            foreach ($arAdquirentesSessao as $inChave => $arAdquirentes) {
                $arListaAdquirentes[$inCountAdq] = $arAdquirentes["codigo"];
                $inTotalQuota += $arAdquirentes["quota"];
                $inCountAdq++;
            }

        }

        $flQuota = str_replace( ".", "", $_POST[ 'nuQuota'  ] );
        $flQuota = str_replace( ",", ".", $flQuota );

        $inTotalQuota += $flQuota;
        $inTotalQuota = floatval($inTotalQuota);

        if ( !in_array( $_POST['inNumCGM'] , $arListaAdquirentes ) ) {
            $rsRecordSet = new Recordset;
            $arAdquirentesSessao = Sessao::read('Adquirentes');
            if ($arAdquirentesSessao) {
                $rsRecordSet->preenche                  ( $arAdquirentesSessao  );
            }
            $rsRecordSet->setUltimoElemento         (                                );
            $inUltimoId    = $rsRecordSet->getCampo ( "inId"                         );

            ++$inUltimoId;
/**/

                $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);
                $obRCIMProprietario->listarProprietariosPorImovel($rsProrprietarios );

                while (!$rsProrprietarios->eof()) {
                    if ($rsProrprietarios->getCampo("numcgm") == $_POST['inNumCGM']) {
                        $flQuotaAnt    = $rsProrprietarios->getCampo("cota"     );
                    }
                    $rsProrprietarios->proximo();
                }

/**/

            $obRCGM->setNumCGM( $_POST[ 'inNumCGM' ] );
            $obRCGM->consultar( $rsCGM );

            $arElementos[ 'inId'        ] = $inUltimoId;
            $arElementos[ 'codigo'      ] = $_POST[ 'inNumCGM' ];
            $arElementos[ 'nome'        ] = $obRCGM->getNomCGM();
            $arElementos[ 'quota_ant'   ] = $flQuotaAnt;
            $arElementos[ 'quota'       ] = $flQuota;
            $arAdquirentesSessao[] = $arElementos;
            $rsRecordSet->preenche( $arAdquirentesSessao );

            Sessao::write('Adquirentes', $arAdquirentesSessao);
            listaAdquirentes       ( $rsRecordSet                  );
        } else {
            $stJs .= 'f.inNumCGM.value = "";';
            $stJs .= 'f.inNumCGM.focus();';
            $stJs .= "alertaAviso('@Adquirente já informado.(".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
        }
    break;
    case "ListaDocumentos":
        $rsRecordSet = new Recordset;
        $obRCIMTransferencia->setCodigoTransferencia( $_POST['inCodigoTransferencia']);
        $obRCIMTransferencia->setCodigoNatureza( $_POST['inCodigoNatureza'] );
        $obRCIMTransferencia->consultarDocumentos();

        $arDocumentosSessao = Sessao::read('Documentos');
        $arDocumentosSessao = $obRCIMTransferencia->getDocumentos();
        Sessao::write('Documentos', $arDocumentosSessao);
        $rsRecordSet->preenche( $arDocumentosSessao );
        listaDocumentos       ( $rsRecordSet                  );
    break;
    case "MontarListas":
        /* Listar Proprietarios */
        if ($_REQUEST["inInscricaoImobiliaria"]) {
            $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);
            /* Recordset com os proprietarios do imovel */
            $rsProrprietarios = new RecordSet;
            $obRCIMProprietario->listarProprietariosPorImovel($rsProrprietarios );
            $arProprietarios = array();
            $inCont = 0;

            while (!$rsProrprietarios->eof()) {
                $inNumCgm   = $rsProrprietarios->getCampo("numcgm"   );
                $flQuota    = $rsProrprietarios->getCampo("cota"     );
                $obRCGM->setNumCGM  ($inNumCgm  );
                $obRCGM->consultar  ( $rsCGM    );
                $arProprietarios[$inCont][ 'inSeq'   ] = $inCont     ;
                $arProprietarios[$inCont][ 'cgm'     ] = $inNumCgm   ;
                $arProprietarios[$inCont][ 'nome'    ] = $obRCGM->getNomCGM();
                $arProprietarios[$inCont][ 'quota'   ] = $flQuota;
                $rsProrprietarios->proximo();
                $inCont++;

            }
            $rsProprietarios = new Recordset;
            $rsProprietarios->preenche($arProprietarios);
            $stJs .=  ListaProprietarios        ( $rsProprietarios , false     );
        } else {
            $arProprietarios = array();
            $rsProprietarios = new Recordset;
        }

        /* Fim de Listar Proprietarios */

        /* Lista de Documentos */

        $rsRecordSet = new Recordset;

        $obRCIMTransferencia->setCodigoTransferencia( $_POST['inCodigoTransferencia']);
        $obRCIMTransferencia->setCodigoNatureza( $_POST['inCodigoNatureza'] );
        $obRCIMTransferencia->consultarDocumentos();

        $arDocumentosSessao = $obRCIMTransferencia->getDocumentos();
if ( is_array($arDocumentosSessao)) {
        Sessao::write('Documentos', $arDocumentosSessao);

        $rsRecordSet->preenche   ( $arDocumentosSessao );

        $stJs .= listaDocumentos ( $rsRecordSet, false, false );
}
        /* Lista de Adquirintes */
        if ($_POST['inCodigoTransferencia']) {
            $rsRecordSet = new Recordset;

            $obRCIMTransferencia->setCodigoTransferencia( $_POST['inCodigoTransferencia'] );
            $obRCIMTransferencia->consultarAdquirentes();

            Sessao::write('Adquirentes', $obRCIMTransferencia->getAdquirentes());
            /* Modificar array para adicionar cota anterior*/

            $rsProrprietarios->setPrimeiroElemento();

            while (!$rsProrprietarios->eof()) {
                $inCont=0;
                $arAdquirentesSessao = Sessao::read('Adquirentes');
                foreach ($arAdquirentesSessao as $inFor) {
                    if ($rsProrprietarios->getCampo("numcgm") == $arAdquirentesSessao[$inCont]['codigo']) {
                        $arAdquirentesSessao[$inCont]['quota_ant'] = $rsProrprietarios->getCampo("cota");
                    }
                $inCont++;
                }

                $rsProrprietarios->proximo();
            }
            $rsRecordSet->preenche    ( Sessao::read('Adquirentes') );

            $stJs .= listaAdquirentes ( $rsRecordSet, false            );
        }
/* Fim do Listar Adquirintes */

    break;

    case "MontarListasBlok":
        /* Listar Proprietarios */
        if ($_REQUEST["inInscricaoImobiliaria"]) {
            $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);
            /* Recordset com os proprietarios do imovel */
            $rsProrprietarios = new RecordSet;
            $obRCIMProprietario->listarProprietariosPorImovel($rsProrprietarios );
            $arProprietarios = array();
            $inCont = 0;

            while (!$rsProrprietarios->eof()) {
                $inNumCgm   = $rsProrprietarios->getCampo("numcgm"   );
                $flQuota    = $rsProrprietarios->getCampo("cota"     );
                $obRCGM->setNumCGM  ($inNumCgm  );
                $obRCGM->consultar  ( $rsCGM    );
                $arProprietarios[$inCont][ 'inSeq'   ] = $inCont     ;
                $arProprietarios[$inCont][ 'cgm'     ] = $inNumCgm   ;
                $arProprietarios[$inCont][ 'nome'    ] = $obRCGM->getNomCGM();
                $arProprietarios[$inCont][ 'quota'   ] = $flQuota;
                $rsProrprietarios->proximo();
                $inCont++;
            }

            $rsProprietarios = new Recordset;
            $rsProprietarios->preenche($arProprietarios);
            $stJs .=  ListaProprietarios        ( $rsProprietarios , false     );
        } else {
            $arProprietarios = array();
            $rsProprietarios = new Recordset;
        }

        /* Fim de Listar Proprietarios */

        /* Lista de Documentos */

        $rsRecordSet = new Recordset;

        $obRCIMTransferencia->setCodigoTransferencia( $_POST['inCodigoTransferencia']);
        $obRCIMTransferencia->setCodigoNatureza( $_POST['inCodigoNatureza'] );
        $obRCIMTransferencia->consultarDocumentos();

        $arDocumentosSessao = $obRCIMTransferencia->getDocumentos();
        if ( is_array($arDocumentosSessao)) {
            Sessao::write('Documentos', $arDocumentosSessao);
            $rsRecordSet->preenche   ( $arDocumentosSessao );
            $stJs .= listaDocumentos ( $rsRecordSet, false, false );
        }

        /* Lista de Adquirintes */
        if ($_POST['inCodigoTransferencia']) {
            $rsRecordSet = new Recordset;

            $obRCIMTransferencia->setCodigoTransferencia( $_POST['inCodigoTransferencia'] );
            $obRCIMTransferencia->consultarAdquirentes();

            Sessao::write('Adquirentes', $obRCIMTransferencia->getAdquirentes());
            /* Modificar array para adicionar cota anterior*/

            $rsProrprietarios->setPrimeiroElemento();

            while (!$rsProrprietarios->eof()) {
                $inCont=0;
                $arAdquirentesSessao = Sessao::read('Adquirentes');
                foreach ($arAdquirentesSessao as $inFor) {
                    if ($rsProrprietarios->getCampo("numcgm") == $arAdquirentesSessao[$inCont]['codigo']) {
                        $arAdquirentesSessao[$inCont]['quota_ant'] = $rsProrprietarios->getCampo("cota");
                    }
                $inCont++;
                }

                $rsProrprietarios->proximo();
            }
            $rsRecordSet->preenche    ( Sessao::read('Adquirentes') );

            $stJs .= listaAdquirentes ( $rsRecordSet, false            );
        }

    //$stJs .= "SismtemaLegado::LiberaFrames();";
    SistemaLegado::executaFrameOculto($stJs);
    SistemaLegado::LiberaFrames();
    exit;
    /* Fim do Listar Adquirintes */

    break;

    case "buscaCGM":

        if ($_POST[ 'inInscricaoImobiliaria' ]) {
            if ($_POST[ 'inNumCGM' ] && $_POST[ 'inNumCGM' ] != '0') {
                $obRCGM->setNumCGM( $_POST[ 'inNumCGM' ] );
                $obRCGM->consultar( $rsCGM );

                $inNumLinhas = $rsCGM->getNumLinhas();
                if ($inNumLinhas <= 0) {
                    $stJs .= 'f.inNumCGM.value = "";';
                    $stJs .= 'f.inNumCGM.focus();   ';
                    $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                    $stJs .= "alertaAviso('@Número do CGM não encontrado. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
                } else {

                    $boExisteCGM = 'f';
                    $arAdquirentesSessao = Sessao::read('Adquirintes');
                    if ( count( $arAdquirentesSessao ) - 1 >= 0 ) {
                        foreach ($arAdquirentesSessao as $inChave => $arAdquirentes) {
                             if ($arAdquirentes["codigo"] == $_POST[ 'inNumCGM' ]) {
                                 $boExisteCGM = 't';
                             }
                        }
                    }
                    if ($boExisteCGM == 'f') {
                        $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);

                        $obRCIMProprietario->listarProprietariosPorImovel($rsProrprietarios );
                        $arProprietarios = array();
                        $inCount = 0;
                        while ( !$rsProrprietarios->eof() ) {
                            if ( $rsProrprietarios->getCampo("promitente") == "f") {
                                $arProprietarios[$inCount] = $rsProrprietarios->getCampo("numcgm");
                            }
                            $inCount++;
                            $rsProrprietarios->proximo();
                        }
                       /* if ( !in_array( $_POST['inNumCGM'] , $arProprietarios ) ) {*/

                            $stNomCgm = $rsCGM->getCampo("nom_cgm");
                            $stJs .= 'd.getElementById("campoInner").innerHTML = "'.$stNomCgm.'";';
                       /* } else {
                            $stJs .= 'f.inNumCGM.value = "";';
                            $stJs .= 'f.inNumCGM.focus();';
                            $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                            $stJs .= "alertaAviso('@O adquirente não pode ser o atual proprietário. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";

                        }*/

                    } else {
                        $stJs .= 'f.inNumCGM.value = "";';
                        $stJs .= 'f.inNumCGM.focus();';
                        $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                        $stJs .= "alertaAviso('@Adquirente já informado. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
                    }
                }
            } elseif ($_POST[ 'inNumCGM' ] == '0') {
                $stJs .= "alertaAviso('@CGM inválido! (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "alertaAviso('@Inscrição Imobiliária não informada!','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.inNumCGM.value = "";';
            $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
            $stJs .= 'f.inInscricaoImobiliaria.focus();';
        }
    break;
    case "buscaCGMFiltro":
        if ($_POST[ 'inNumCGM' ] != '' && $_POST[ 'inNumCGM' ] != '0') {
            $obRCGM->setNumCGM( $_POST[ 'inNumCGM' ] );
            $obRCGM->consultar( $rsCGM );

            $inNumLinhas = $rsCGM->getNumLinhas();
            if ($inNumLinhas <= 0) {
                $stJs .= 'f.inNumCGM.value = "";';
                $stJs .= 'f.inNumCGM.focus();';
                $stJs .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Número do CGM não encontrado. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $stJs .= 'd.getElementById("campoInner").innerHTML = "'.$stNomCgm.'";';
            }
        } elseif ($_POST[ 'inNumCGM' ] == '0') {
            $stJs .= "alertaAviso('@CGM inválido! (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
        }
    break;
    case "buscaInscricao":
        $rsRecordSet = new RecordSet;
        if ($_POST['inInscricaoImobiliaria']) {
            $obRCIMImovel->setNumeroInscricao( $_POST['inInscricaoImobiliaria'] );
            $obRCIMImovel->listarImoveisAtivos( $rsRecordSet );
            if ( $rsRecordSet->getNumLinhas() <= 0 ) {
                $stJs .= 'f.inInscricaoImobiliaria.value = "";';
                $stJs .= 'f.inInscricaoImobiliaria.focus();';
                $stJs .= "alertaAviso('@Inscrição Imobiliária não encontrada. (".$_POST["inInscricaoImobiliaria"].")','form','erro','".Sessao::getId()."');";
                $stJs .=  'LiberaFrames(true,false);';
            } else {
                $obRCIMTransferencia->setInscricaoMunicipal( $_POST['inInscricaoImobiliaria'] );
                $obRCIMTransferencia->listarTransferencia( $rsRecordSet );
                if( $rsRecordSet->getNumLinhas() > 0
                && $rsRecordSet->getCampo("dt_efetivacao") == ""
                && $rsRecordSet->getCampo("dt_cancelamento") == ""){
                    $stJs .= 'f.inInscricaoImobiliaria.value = "";';
                    $stJs .= 'f.inInscricaoImobiliaria.focus();';
                    $stJs .= "alertaAviso('@Inscrição Imobiliária já possui transferência. (Inscrição Imobiliária: ".$_POST["inInscricaoImobiliaria"].")','form','erro','".Sessao::getId()."');";
                    $stJs .=  'LiberaFrames(true,false);';
                } else {
                    $stJs .= 'f.inInscricaoImobiliaria.readOnly = true;';
                    /*********************************************************************************************/

                    /* Listar Proprietarios */

                    /* Se estiver tudo certo, busca proprietarios do imovel */

                    $obRCIMImovel->setNumeroInscricao($_REQUEST["inInscricaoImobiliaria"]);
                    /* Recordset com os proprietarios do imovel */

                    $obRCIMProprietario->listarProprietariosPorImovel($rsProrprietarios );
                    $arProprietarios = array();
                    $inCont = 0;
                    while (!$rsProrprietarios->eof()) {

                        $inNumCgm   = $rsProrprietarios->getCampo("numcgm"   );
                        $flQuota    = $rsProrprietarios->getCampo("cota"     );
                        $obRCGM->setNumCGM  ($inNumCgm  );
                        $obRCGM->consultar  ( $rsCGM    );
                        $arProprietarios[$inCont][ 'inSeq'   ] = $inCont     ;
                        $arProprietarios[$inCont][ 'cgm'     ] = $inNumCgm   ;
                        $arProprietarios[$inCont][ 'nome'    ] = $obRCGM->getNomCGM();
                        $arProprietarios[$inCont][ 'quota'   ] = $flQuota;
                        $rsProrprietarios->proximo();
                        $inCont++;

                    }

                    $rsProprietarios = new Recordset;
                    $rsProprietarios->preenche($arProprietarios);
                    $stJs .=  ListaProprietarios( $rsProprietarios , false );
                    $stJs .=  'LiberaFrames(true,false);';
                }
            }
        }
    break;
    case "buscaProcesso":
        if ($_POST['inProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();

            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inProcesso.value = "";';
                $stJs .= 'f.inProcesso.focus();';
                $stJs .= "alertaAviso('@Processo não encontrado. (".$_POST["inProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
    break;
    case "buscaCreci":
        $obRCIMCorretagem = new RCIMCorretagem;
        $rsCorretagem     = new RecordSet;
        if ($_REQUEST["stCreci"]) {
            $obRCIMCorretagem->setRegistroCreci( $_REQUEST["stCreci"]);
            $obRCIMCorretagem->buscaCorretagem ( $rsCorretagem       );
            if ( $rsCorretagem->eof() ) {
                $stJs .= "d.getElementById('stNomeCreci').innerHTML = '&nbsp;';";
                $stJs .= "erro = true;\n";
                $stJs .= "mensagem += 'Registro Creci inválido!';\n";
                $stJs .= "alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');\n";

    } else {
                $stJs .= 'd.getElementById("stNomeCreci").innerHTML = "'.$rsCorretagem->getCampo("nom_cgm").'";';
            }
        }
    break;
    case "excluiAdquirente":
        $rsRecordSet = new Recordset;

        $id = $_GET['inId'];
        $arAdquirentesSessao = Sessao::read('Adquirentes');

//        $arAdquirentesSessao = reset($arAdquirentesSessao);
//        Sessao::write('Adquirente', $arAdquirentesSessao);

        $arTMP = array();

        while ( list( $arId ) = each( $arAdquirentesSessao ) ) {
            if ($arAdquirentesSessao[ $arId ][ 'inId' ] != $id) {
                $arElementos[ 'inId'   ] = $arAdquirentesSessao[ $arId ][ 'inId'   ];
                $arElementos[ 'codigo' ] = $arAdquirentesSessao[ $arId ][ 'codigo' ];
                $arElementos[ 'nome'   ] = $arAdquirentesSessao[ $arId ][ 'nome'   ];
                $arElementos[ 'quota'  ] = $arAdquirentesSessao[ $arId ][ 'quota'  ];
                $arTMP[] = $arElementos;
            }
        }
        Sessao::write('Adquirentes', $arTMP);

        if ( count( $arAdquirentesSessao ) - 1 >= 0 ) {
            $rsRecordSet->preenche( $arTMP );
        }
        $stJs .= listaAdquirentes( $rsRecordSet );
    break;
    case "limparFormulario":
        $arAdquirentesSessao = array();
        $arDocumentosSessao  = array();
        Sessao::write('Adquirente', $arAdquirentesSessao);
        Sessao::write('Documentos', $arDocumentosSessao);

        $stJs .= "d.frm.inInscricaoImobiliaria.readOnly = false;";
        $stJs .= "d.frm.reset();";
        $stJs .= "d.getElementById('spnDocumentosNatureza').innerHTML = '';";
        $stJs .= "d.getElementById('campoInner').innerHTML = '&nbsp;';";
        $stJs .= "d.getElementById('stNomeCreci').innerHTML = '&nbsp;';";
        $stJs .= "d.getElementById('spnAdquirentes').innerHTML = '';";
        $stJs .= "d.getElementById('spnProprietarios').innerHTML = '';";
        $stJs .= "d.frm.inInscricaoImobiliaria.focus();";
    break;
    case "validaData":
        if ($_REQUEST["hdnDataCadastro"] == "") {
            $stDataLimite = "15000421";
        } else {
            $arData = preg_split( "/\//",$_REQUEST["hdnDataCadastro"]);
            $stDataLimite = $arData[2].$arData[1].$arData[0];
        }
        $stDataEfetivacao = $_REQUEST["stDataEfetivacao"];
        $stDiaEfetivacao = substr($stDataEfetivacao,0,2);
        $stMesEfetivacao = substr($stDataEfetivacao,3,5);
        $stAnoEfetivacao = substr($stDataEfetivacao,6);
        $stDataEfetivacao = $stAnoEfetivacao.$stMesEfetivacao.$stDiaEfetivacao;
        if ($stDataEfetivacao < $stDataLimite) {
            $stJs .= "    erro = true;                                                                      ";
            if ($_REQUEST["hdnDataCadastro"] == "") {
                $stJs .= "    f.stDataEfetivacao.value=\"\";                                                ";
                $stJs .= "    mensagem += \"@Campo Data da Inscrição deve ser posterior a 21/04/1500!\";    ";
            } else {
                $stJs .= "    f.stDataEfetivacao.value=\"".$_REQUEST["hdnDataCadastro"]."\";                ";
                $stJs .= "    mensagem += \"@Campo Data da Inscrição deve ser posterior a ".$_REQUEST["hdnDataCadastro"]."!\";    ";
            }
            $stJs .= "    alertaAviso(mensagem,'form','erro','".Sessao::getId()."', '../');                     ";
            $stJs .= "    f.stDataEfetivacao.focus();                                                       ";
        }
    break;

    case "atualizaCheckDocumento":
        $arDocumentosSessao = Sessao::read('Documentos');
        foreach ($arDocumentosSessao as $listaDocumentos) {
            $posicaoSessao = $listaDocumentos["inId"] - 1;
            $posicaoID     = $listaDocumentos["inId"];
            if ($_REQUEST["boEntregue_$posicaoID"]) {
                $arDocumentosSessao[$posicaoSessao]['entregue'] = "t";
            } else {
                $arDocumentosSessao[$posicaoSessao]['entregue'] = "f";
            }
        }
        Sessao::write('Documentos', $arDocumentosSessao);
        VerificaDocumentacaoEntregue( $arDocumentosSessao );
        //montaCheckBoxAvaliacao( VerificaDocumentacaoEntregue( $sessao->transf['Documentos'] ) );

    break;

    case "AtualizaSegueAvaliacao":
    break;
}
if( $stJs )
    SistemaLegado::executaFrameOculto($stJs);

if ($stAcao == 'alterar' && $stCtrl != 'AtualizaSegueAvaliacao') {
    //montaCheckBoxAvaliacao( VerificaDocumentacaoEntregue( $sessao->transf['Documentos'] ) );
    $arDocumentosSessao = Sessao::read('Documentos');
    VerificaDocumentacaoEntregue( $arDocumentosSessao );
}

?>
