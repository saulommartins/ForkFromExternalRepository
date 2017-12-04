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
    * Arquivo de instância para manutenção dos processos
    * Data de Criação: 11/10/2006

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    Casos de uso: uc-01.06.98

    $Id: PRManterProcesso.php 62581 2015-05-21 14:05:03Z michel $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcesso";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId();
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $_REQUEST['stAcao'] ? $_REQUEST['stAcao'] : 'alterar';

switch ($stAcao) {
    case 'alterar':
        include_once(CAM_GA_PROT_MAPEAMENTO."TPRODocumentoAssunto.class.php");
        include_once(CAM_GA_PROT_MAPEAMENTO."TPRODocumentoProcesso.class.php" );
        include_once(CAM_GA_PROT_MAPEAMENTO."TPROCopiaDigital.class.php" );

        Sessao::setTrataExcecao(true);

        //SEPARA A CHAVE DO PROCESSO
        $arProcesso = explode('/',$_POST['stChaveProcesso'] );

        //BUSCA OS DOCUMENTOS EXISTENTES DO PROCESSO
        $obTPRODocumentoProcesso = new TPRODocumentoProcesso();
        $obTPRODocumentoProcesso->setDado('cod_processo', (int) $arProcesso[0]);
        $obTPRODocumentoProcesso->setDado('exercicio',$arProcesso[1]);
        $obTPRODocumentoProcesso->setComplementoChave('cod_processo,exercicio');
        $obTPRODocumentoProcesso->recuperaPorChave($rsDocumentos);
        $arDocumentosApagar = array();//ARMAZENA OS DOCUMENTOS QUE SERÃO APAGADOS POR NÃO HAVER MAIS RELAÇÃO COM O PROCESSO
        while ( !$rsDocumentos->eof() ) {
            $arDocumentosApagar[$rsDocumentos->getCampo('cod_documento')] = true;
            $rsDocumentos->proximo();
        }
        $arDocumentosIncluir = array();

        //GERA O NOME DOS DIRETORIOS QUE DEVEM CONTER ARQUIVOS
        $arDiretorios = array();
        if ( is_array($_POST['arCodigoDocumento'])) {
            foreach ($_POST['arCodigoDocumento'] as $inChave => $inCodigoDocumento) {
                $arDiretorios[] = $inCodigoDocumento.'_'.(int) $arProcesso[0].'_'.$arProcesso[1];
                if ( isset($arDocumentosApagar[$inCodigoDocumento]) ) {
                    $arDocumentosApagar[$inCodigoDocumento]=false;//seta como false para não apagar estes documentos
                } else {
                    $arDocumentosIncluir[$inCodigoDocumento] = true;
                }
            }
        }
        //SETA OS VALORES PERSISTIDOS NA CLASSE PARA FAZER A ALTERAÇÃO
        include_once(CAM_GA_PROT_MAPEAMENTO.'TProcesso.class.php');
        $obTPROProcesso = new TProcesso();
        $obTPROProcesso->setDado('cod_processo', (int) $arProcesso[0]);
        $obTPROProcesso->setDado('ano_exercicio',$arProcesso[1]);
        $obTPROProcesso->consultar();

        //GRAVA O HISTÓRICO DAS ALTERAÇÕES
        include_once(CAM_GA_PROT_MAPEAMENTO.'TPROProcessoHistorico.class.php');
        $obTPROProcessoHistorico = new TPROProcessoHistorico();
        $obTPROProcessoHistorico->setDado('cod_processo',      $obTPROProcesso->getDado('cod_processo')     );
        $obTPROProcessoHistorico->setDado('ano_exercicio',     $obTPROProcesso->getDado('ano_exercicio')    );
        $obTPROProcessoHistorico->setDado('cod_classificacao', $obTPROProcesso->getDado('cod_classificacao'));
        $obTPROProcessoHistorico->setDado('cod_assunto',       $obTPROProcesso->getDado('cod_assunto')      );
        $obTPROProcessoHistorico->setDado('observacoes',       $obTPROProcesso->getDado('observacoes')      );
        $obTPROProcessoHistorico->setDado('resumo_assunto',    $obTPROProcesso->getDado('resumo_assunto')   );
        $obTPROProcessoHistorico->inclusao();

        Sessao::getTransacao()->setMapeamento($obTPROProcesso);
        
        $centroCusto = (isset($_REQUEST["centroCusto"])) ? $_REQUEST["centroCusto"] : 'NULL';

        //FAZ A ALTERAÇÃO NOS DADOS DO PROCESSO
        $obTPROProcesso->setDado('observacoes',      $_REQUEST['stObservacoes']    );
        $obTPROProcesso->setDado('resumo_assunto',   $_REQUEST['stResumo']         );
        $obTPROProcesso->setDado('cod_classificacao',$_REQUEST['codClassificacao'] );
        $obTPROProcesso->setDado('cod_assunto',      $_REQUEST['codAssunto']       );
        $obTPROProcesso->setDado('cod_centro',       $centroCusto                  );
        $obTPROProcesso->alteracao();

        //EXCLUI DA TABELA SW_DOCUMENTO_PROCESSO OS DOCUMENTO QUE NÃO TEM RELAÇÃO COM O ASSUNTO SELECIONADO
        foreach ($arDocumentosApagar AS $inCodigoDocumento => $boValor) {
            if ($boValor) {
                $obTPRODocumentoProcesso = new TPRODocumentoProcesso();
                $obTPRODocumentoProcesso->setDado('cod_documento',$inCodigoDocumento);
                $obTPRODocumentoProcesso->setDado('cod_processo', (int) $arProcesso[0]);
                $obTPRODocumentoProcesso->setDado('exercicio',$arProcesso[1]);
                $obTPRODocumentoProcesso->exclusao();
                unset($obTPRODocumentoProcesso);
            }
        }
        //INCLUI NA TABEBLA SW_DOCUMENTO_PROCESSO OS DOCUMENTOS QUE TEM RELAÇÃO COM O ASSUNTO SELECIONADO
        foreach ($arDocumentosIncluir As $inCodigoDocumento => $boValor) {
            $obTPRODocumentoProcesso = new TPRODocumentoProcesso();
            $obTPRODocumentoProcesso->setDado('cod_documento',$inCodigoDocumento);
            $obTPRODocumentoProcesso->setDado('cod_processo', (int) $arProcesso[0]);
            $obTPRODocumentoProcesso->setDado('exercicio',$arProcesso[1]);
            $obTPRODocumentoProcesso->inclusao();
            unset($obTPRODocumentoProcesso);
        }

        //MONTA O NOME DO DIRETORIO TEMPORARIO UTILIZANDO O ID DA SESSÃO
        $inPosInicial = strpos(Sessao::getId(),'=') + 1;
        $inPosFinal = strpos(Sessao::getId(),'&') - $inPosInicial;
        $stIdSessao = substr(Sessao::getId(),$inPosInicial,$inPosFinal );
        $stDiretorioSessao = CAM_PROTOCOLO."tmp/".$stIdSessao;
        if ( is_dir($stDiretorioSessao) ) {
            $rcDirTmp = opendir($stDiretorioSessao);
            foreach ($arDiretorios as $stNomeDiretorio) {
                if (is_dir($stDiretorioSessao.'/'.$stNomeDiretorio) ) {
                    $rcDirDocumento = opendir($stDiretorioSessao.'/'.$stNomeDiretorio);
                    $arChaveDocumento = explode("_", $stNomeDiretorio);
                    $obTPROCopiaDigital = new TPROCopiaDigital();
                    $obTPROCopiaDigital->setDado('cod_documento',$arChaveDocumento[0]);
                    $obTPROCopiaDigital->setDado('cod_processo',$arChaveDocumento[1]);
                    $obTPROCopiaDigital->setDado('exercicio',$arChaveDocumento[2]);
                    while ( ($rcArquivo = readdir($rcDirDocumento)) !== false ) {
                        if ($rcArquivo != "." && $rcArquivo != "..") {
                            $obTPROCopiaDigital->proximoCod($inCodigoCopia);
                            $stExtencao = substr($rcArquivo,strrpos($rcArquivo,'.') );

                            if ($stExtencao == '.jpg' or $stExtencao == '.jpeg') {
                                $boImagem = 't';
                            } else {
                                $boImagem = 'f';
                            }

                            $stNomeArquivoDestino = $inCodigoCopia.'_'.$arChaveDocumento[0].'_'.$arChaveDocumento[1].'_'.$arChaveDocumento[2].$stExtencao;
                            $obTPROCopiaDigital->setDado('cod_copia',$inCodigoCopia);
                            $obTPROCopiaDigital->setDado('imagem',$boImagem);
                            $obTPROCopiaDigital->setDado('anexo',$stNomeArquivoDestino);
                            $obTPROCopiaDigital->inclusao();
                            $stNomeArquivo = $stDiretorioSessao.'/'.$stNomeDiretorio."/".$rcArquivo;
                            $stNomeDestino = CAM_PROTOCOLO."anexos/".$stNomeArquivoDestino;
                            //MOVE OS ARQUIVOS DO DIRETÓRIO TEMPORÁRIO PARA O DIRETÓRIO ANEXOS
                            $stMensagem = "Erro ao fazer a copia do arquivo ".$rcArquivo."!";

                            if (!is_writable(CAM_PROTOCOLO."anexos/")) {
                                $stMensagem .= " O diretório ".CAM_PROTOCOLO."anexos não possui permissão de escrita!";
                                Sessao::getExcecao()->setDescricao($stMensagem);
                            } else {
                                if ( !copy( $stNomeArquivo, $stNomeDestino) ) {
                                    Sessao::getExcecao()->setDescricao($stMensagem);
                                } else {
                                    chmod($stNomeDestino,0777);
                                }
                            }
                        }
                    }
                    closedir($rcDirDocumento);
                }

                if ( is_dir($stDiretorioSessao.'/'.$stNomeDiretorio) ) {
                    $rcDirDocumento = opendir($stDiretorioSessao.'/'.$stNomeDiretorio);
                    while ( ($rcArquivo = readdir($rcDirDocumento)) !== false ) {
                        if ( $rcArquivo != '.' and  $rcArquivo != '..' and is_file($stDiretorioSessao.'/'.$stNomeDiretorio.'/'.$rcArquivo)) {
                            if ( !unlink($stDiretorioSessao.'/'.$stNomeDiretorio.'/'.$rcArquivo) ) {
                                Sessao::getExcecao()->setDescricao("Erro ao excluir o arquivo temporário ".$rcArquivo." !");
                            }
                        }
                    }
                    closedir($rcDirDocumento);
                }

                if ( is_dir($stDiretorioSessao.'/'.$stNomeDiretorio)) {
                    rmdir($stDiretorioSessao.'/'.$stNomeDiretorio);
                }
            }
            closedir($rcDirTmp);

            rmdir($stDiretorioSessao);
        }

        Sessao::encerraExcecao();

        $arChaveOrgao = explode( '-', $_POST['stChaveOrgao'] );

        include_once (CAM_FW_LEGADO."processosLegado.class.php"  ); //Classe que manipula os dados do processo
        include_once (CAM_FW_LEGADO."dataBaseLegado.class.php"  ); //Classe que manipula os dados do processo
        $obProcessoLegado = new processosLegado();
        $arProcesso = $obProcessoLegado->pegaDados($obTPROProcesso->getDado('cod_processo'),$obTPROProcesso->getDado('ano_exercicio'));

        $arParametros["iCodProcesso"]     = $arProcesso['codProcesso'];
        $arParametros["sAnoExercicio"]    = $arProcesso['anoExercicio'];
        $arParametros["codClassif"]       = $arProcesso['codClassif'];
        $arParametros["codAssunto"]       = $arProcesso['codAssunto'];
        $arParametros["observacoes"]      = $arProcesso['observacoes'];

        $arParametros["codSituacao"]      = SistemaLegado::pegaDado("cod_situacao", "sw_processo","where cod_processo = ".$arProcesso['codProcesso']." and ano_exercicio = '".$arProcesso['anoExercicio']."' ");

        //DADOS DO SETOR DO USUÁRIO QUE ESTA FAZENDO A ALTERAÇÃO NO PROCESSO
        if ($arParametros["codSituacao"] == '2') {
            $arParametros["codOrgao"]         = $_REQUEST['hdnUltimoOrgaoSelecionado'];
            # $arParametros["codUnidade"]       = $_POST['stChaveUnidade'];
            # $arParametros["codDpto"]          = $_POST['stChaveDepartamento'];
            # $arParametros["codSetor"]         = $_POST['stChaveSetor'];
            # $arParametros["anoExercicio"]     = $arChaveOrgao[1];
            # $arParametros["nomSetor"]         = SistemaLegado::pegaDado("nom_setor","administracao.setor","Where cod_setor = '".$_POST['stChaveSetor']."' And cod_departamento = '".$_POST['stChaveDepartamento']."' And cod_unidade = '".$_POST['stChaveUnidade']."' And cod_orgao = '".$arChaveOrgao[0]."' And ano_exercicio = '".$arChaveOrgao[1]."' ");
        } else {
            $arParametros["codOrgao"]         = Sessao::read('codOrgao');
            # $arParametros["codUnidade"]       = Sessao::read('codUnidade');
            # $arParametros["codDpto"]          = Sessao::read('codDpto');
            # $arParametros["codSetor"]         = Sessao::read('codSetor');
            # $arParametros["anoExercicio"]     = Sessao::read('anoExercicio');
            # $arParametros["nomSetor"]         = $arProcesso[nomSetor];
        }
        $arParametros["nomAssunto"]       = $arProcesso[assunto];
        $arParametros["nomClassificacao"] = $arProcesso[classificacao];
        $stFiltro = "where cod_processo=".$arProcesso[codProcesso]." and ano_exercicio='".$arProcesso['anoExercicio']."'";
        $arParametros["numMatricula"]     = SistemaLegado::pegaDado("num_matricula","sw_processo_matricula", $stFiltro );
        $arParametros["numInscricao"]     = SistemaLegado::pegaDado("num_inscricao","sw_processo_inscricao", $stFiltro );
        $arParametros["vinculo"]          = $arParametros["numMatricula"] ? "imobiliaria" : ($arParametros["numInscricao"] ? "inscricao" : "cgm" );
        $arParametros["numCgm"]           = $arProcesso[numCgm];

        if ($valorAtributo == "") {
            $valorAtributo = array();
        }

        $arLink       = Sessao::read("filtro");
        $codProcesso  = $arLink["inCodigoProcesso"];
        $anoExercicio = $arLink["inAnoExercicio"];
/**/
        while (list($key, $val) = each($valorAtributo)) {
            $sqlAtributo .=	"UPDATE
                                sw_assunto_atributo_valor
                             SET
                                valor = '".$valorAtributo[$key]."'
                             WHERE
                                cod_atributo = ".$key."                   AND
                                cod_assunto = ".$codAssunto."             AND
                                cod_classificacao = ".$codClassificacao." AND
                                cod_processo = ".$codProcesso."           AND
                                exercicio = '".$anoExercicio."'; ";
        }
        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->executaSql($sqlAtributo);

        /*
         * Foi inserido essa consulta para retornar o ultimo andamento
         * da sw_ultimo_andamento, para que saiba qual o andamento
         * a ser atualizado. Desconsiderar o andamento 0 = cadastro do processo.
         *
         */
        $sqlUltimoAndamento = " SELECT  cod_andamento
                                  FROM  sw_ultimo_andamento
                                 WHERE  cod_processo = ".$codProcesso."
                                   AND  ano_exercicio = '".$anoExercicio."'; ";

        $conn = new dataBaseLegado ;
        $conn->abreBD();
        $conn->executaSql($sqlUltimoAndamento);
        $rsUltimoAndamento = $conn->abreSelecao($sqlUltimoAndamento);
        $inCodUltimoAndamento = $conn->pegaCampo("cod_andamento");

        if ($arParametros["codSituacao"] == '2') {

            if (!empty($inCodUltimoAndamento)) {
                $sql .=	"UPDATE
                                sw_andamento
                             SET
                                cod_orgao = '".$_REQUEST['hdnUltimoOrgaoSelecionado']."'
                             WHERE
                                cod_processo = ".$codProcesso." AND
                                ano_exercicio = '".$anoExercicio."' AND
                                cod_andamento = ".$inCodUltimoAndamento." ";

                $conn = new dataBaseLegado ;
                $conn->abreBD();
                $conn->executaSql($sql);
            }

        } elseif ($arParametros["codSituacao"] == '3') {

            if ($inCodUltimoAndamento == 0) {
                $sql = "INSERT INTO sw_andamento VALUES (1, ".$codProcesso.", '".$anoExercicio."', ".$_REQUEST['hdnUltimoOrgaoSelecionado'].", ".$arProcesso['codUsuario'].")";
                $conn = new dataBaseLegado ;
                $conn->abreBD();
                $conn->executaSql($sql);

                // Atualiza a situação do processo.
                $obTPROProcesso->setDado('cod_situacao', 2);
                $obTPROProcesso->alteracao();

                $arParametros["codSituacao"] = 2;
            }
        }

        //Sessao::write('arParametros', array());
        Sessao::write('arParametros',$arParametros);

        if ( ($_REQUEST['hdnUltimoOrgaoSelecionado'] != '') && $arParametros["codSituacao"] != '2') {
            SistemaLegado::alertaAviso("LSManterProcesso.php?stParametros=sessao&codProcesso=".$codProcesso."&anoExercicio=".$anoExercicio,"Erro ao alterar processos (Processo ".$codProcesso."/".$anoExercicio." recebido por outro usuário)",'unica', 'erro', Sessao::getId(),'../');
            break;
        } else {
            $processo = explode("/",$_POST['stChaveProcesso']);
            $codProcesso  = $processo[0];
            $anoExercicio = $processo[1];
            SistemaLegado::alertaAviso("imprimeReciboProcesso.php?stParametros=sessao&codProcesso=".$codProcesso."&anoExercicio=".$anoExercicio,'Processo: '.$_POST['stChaveProcesso'],'alterar', 'aviso', Sessao::getId(),'../');
            break;
        }
}

?>
