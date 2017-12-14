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
    * Página de formulário do Manter vinculo atividades documentos
    * Data de Criacao: 30/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

*/

include_once(CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php");
require_once 'VFISProcessoFiscal.class.php';
include_once(CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php");
include_once(CAM_GT_FIS_NEGOCIO."RFISVinculo.class.php");
include_once(CAM_GT_FIS_COMPONENTES."IFISTextBoxSelectDocumento.class.php");

class VFISManterVinculo
{
    private $controller;
    private $visaoProcessoFiscal;
    private $obRFISVinculo;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->visaoProcessoFiscal = new VFISProcessoFiscal($this->controller);
        $this->obRFISVinculo = new RFISVinculo;
        $this->obMontaAtividade = new MontaAtividade;
        $this->obMontaAtividade->setCadastroAtividade(false);
    }

    public function montaForm($param)
    {
        $obForm = new Form();

        //Define o formulario
        $obFormulario = new Formulario;
        $obFormulario->addForm ( $obForm );

        if ($_GET['cmbTipoFiscalizacao']== '1' or $_GET['cmbTipoFiscalizacao']== '2') {

            $obIPopUpDocumento = new IFISTextBoxSelectDocumento($_GET['cmbTipoFiscalizacao']."AND uso_interno = 'f' ");
            $obIPopUpDocumento->obCmbDocumento->setNull(true);
            $obIPopUpDocumento->obTxtDocumento->setRotulo ('*Documento');
            $obIPopUpDocumento->obCmbDocumento->setTitle("Informe o código do documento.");
            $obIPopUpDocumento->obTxtDocumento->setId("txtDocumentos");
            $obIPopUpDocumento->obCmbDocumento->setId("cmbDocumentos");
            $obIPopUpDocumento->geraFormulario($obFormulario);
            $obFormulario->montaInnerHTML();
            $stJs = "$('spnForm').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $stJs = "$('spnForm').innerHTML = '';";
        }

        return $stJs;
    }

    //INCLUI LISTA DE DOCUMENTOS NO FORMULÁRIO
    public function incluirDocumento($param)
    {
        $opt = array(
            "cabecalho" => "Lista de Documentos",
            "span"      => "spnDocumentos",
            "desc"      => "txtDocumentos",
            "alvo"      => "cmbDocumentos",
            "codigo"    => $param["inDocumento"],
            "container" => "arDocumentos"
            );

        $arValores = Sessao::read($opt['container']);

        if ($this->visaoProcessoFiscal->PodeIncluirItemLista($opt)) {

            $obDocumento = $this->controller->getDocumento(" documento.cod_documento = ".$opt["codigo"]);

            $js = " parent.document.getElementById('txtDocumentos').value = '&nbsp';\n";
            $js.= " parent.window.document.getElementById('txtDocumentos').innerHTML ='&nbsp';\n";

            if ($obDocumento) {

                $k = count( $arValores );
                $arValores[$k]['codigo'] = $obDocumento->arElementos[0]['cod_documento'];
                $arValores[$k]['nome'] = $obDocumento->arElementos[0]['nom_documento'];
                $Hdn = $this->visaoProcessoFiscal->GeraHidden("cod_documento",$arValores[$k]['codigo']);
                $arValores[$k]['hidden'] = $Hdn;
                Sessao::write($opt['container'], $arValores);

                $lista = $this->montaLista($arValores,'',$opt);
                $result = $lista;

            } else {

                $stMensagem = "@Documento inválido (".$param["inDocumento"].")   ";
                $js.= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
                $result = $js;
            }

        } else {

            $stMensagem = "@Documento já informado.(".$param["inDocumento"].")   ";
            $js.= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');          \n";
            $result = $js;
        }

        return $result;
    }

    //EXCLUI DOCUMENTOS DA LISTA DO FORMULARIO E GUARDA NA SESSAO COD DE DOCUMENTOS A SEREM EXCLUIDOS NA BASE
    public function ExcluirItemLista($param)
    {
        $arRetorno = array();
        $k = 0;
        $e = 0;
        $key = trim($param["inId"]);

        $arValores = Sessao::read($param['container']);
        if (is_array($arValores)) {
            foreach ($arValores as $value) {
                $keyValue = trim($value['codigo']);
                if ($key !== $keyValue) {
                    $arRetorno[$k]['codigo'] = $value['codigo'];
                    $arRetorno[$k]['nome'] = $value['nome'];
            $arRetorno[$k]['hidden'] = $value['hidden'];
                    $k++;
                } else {
                    $arExclusao[$e]['excluir'] = $key;
                    Sessao::write('exclusao', $arExclusao);
                    $e++;
                }
            }
        }
        Sessao::write($param['container'], $arRetorno);
        $result = $this->montaLista($arRetorno, '', $param);

        return $result;
    }

    //REFERENCIA A REGRA DE NEGÓCIO
    public function vincular($param)
    {
        //BUSCA CODIGO DA ATIVIVIDADE COM REFERENCIA A MASCARA SOLICITADA
        //echo $param['stChaveAtividade'];
        if ($param['stChaveAtividade']) {
            $cod_atividade = $this->obRFISVinculo->getAtividade($param['stChaveAtividade']);
        } else {
            return sistemaLegado::exibeAviso("Todos os Níveis da Atividade devem ser preenchidos.","n_incluir","aviso");
        }

        return $this->obRFISVinculo->setVinculo($cod_atividade, $param);

    }

    //BUSCA DOCUMENTOS JÁ VINCULADOS A ATIVIDADE
    public function buscaDocumentos($param)
    {
        $obRFISVinculo = new RFISVinculo;

        $codNivel = $param['inNumNiveis']-1;
        $codNivelAtividade = $param["inCodAtividade_".$codNivel];
        $arCod = explode('§', $codNivelAtividade);

        $opt = array(
                    "cabecalho" => "Lista de Documentos",
                    "span"      => "spnDocumentos",
                    "desc"      => "txtDocumentos",
                    "alvo"      => "cmbDocumentos",
                    "codigo"    => "cod_documento",
                    "container" => "arDocumentos"
                    );

        $arValores = Sessao::read($opt['container']);

        if ($arCod != 0) {
            $Documentos = $obRFISVinculo->getDocumento($arCod[1]);

            $documentos = $Documentos->arElementos;

            if ($this->visaoProcessoFiscal->PodeIncluirItemLista($opt)) {

                $js = " parent.document.getElementById('txtDocumentos').value = '&nbsp';\n";
                $js.= " parent.window.document.getElementById('txtDocumentos').innerHTML ='&nbsp';\n";

                if ($documentos) {
                    $cont = 0;
                    foreach ($documentos as $doc) {
                        $arValores[$cont]['codigo' ] = $doc['cod_documento'];
                        $arValores[$cont]['nome'] = $doc['nom_documento'];
                        $Hdn = $this->visaoProcessoFiscal->GeraHidden("cod_documento",$arValores[$cont]['codigo' ]);
                        $arValores[$cont]['hidden'] = $Hdn;
                        $cont++;
                    }
                    Sessao::write($opt['container'], $arValores);
                    $lista = $this->montaLista($arValores, '', $opt);
                    $result = $lista;
                }
            }
        } else {
            $arValores = array();
            Sessao::write('arDocumentos', array());
            Sessao::write($opt['container'], $arValores);
            $lista = $this->montaLista($arValores, '', $opt);
            $result = $lista;
        }

        return $result;

    }

    //MONTA LISTA DE DOCUMENTOS NO FORMULÁRIO
    public function montaLista($arValores, $stAcao = '',$opt)
    {
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche($arValores);

        $obLista = new Lista;
        $obLista->setMostraPaginacao(false);
        $obLista->setTitulo($opt["cabecalho"]);

        $obLista->setRecordSet($rsRecordSet);

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth(5);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Código");
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Nome");
        $obLista->ultimoCabecalho->setWidth(80);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Ação");
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();

        ////dados

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo("codigo");
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo("[nome] [hidden]");
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao("EXCLUIR");
        $obLista->ultimaAcao->setFuncao(true);
        $obLista->ultimaAcao->setLink("javascript: executaFuncaoAjax('ExcluirItemLista');");
        $obLista->ultimaAcao->addCampo("","&inId=[codigo]&span={$opt['span']}&cabecalho={$opt['cabecalho']}&desc={$opt['desc']}&alvo={$opt['alvo']}&container={$opt['container']}");
        $obLista->commitAcao();

        $obLista->montaHTML();

        $html = $obLista->getHTML();
        $html = str_replace("\n","",$html);
        $html = str_replace("  ","",$html);
        $html = str_replace("'","\\'",$html);

        $stJs.= " d.getElementById('{$opt['span']}').innerHTML  = '".$html."'; \n";

        return $stJs;
    } //fim montalista

    //PREENCHE COMBOS NA FILTRAGEM DE ATIVIDADES
    public function preencheProxCombo()
    {
        $stNomeComboAtividade = "inCodAtividade_".($_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
        $inPosicao = $_REQUEST["inPosicao"];

        if (empty($stChaveLocal) and $_REQUEST["inPosicao"] > 2) {
            $stNomeComboAtividade = "inCodAtividade_".($_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("§" , $stChaveLocal);
        $this->obMontaAtividade->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]);
        $this->obMontaAtividade->setCodigoNivel($arChaveLocal[0]);
        $this->obMontaAtividade->setCodigoAtividade($arChaveLocal[1]);
        $this->obMontaAtividade->setValorReduzido($arChaveLocal[3]);
        $this->obMontaAtividade->preencheProxCombo($inPosicao, $_REQUEST["inNumNiveis"]);

        $arParam['stChaveAtividade'] = "";
        //$stCampo = substr($this->buscaDocumentos($arParam), 2);
        return "<script> parent.telaPrincipal.document".$stCampo."</script>";
    }

    public function preencheCombosAtividade()
    {
        $this->obMontaAtividade->setCodigoVigencia($_REQUEST["inCodigoVigencia"]);
        $this->obMontaAtividade->setCodigoNivel($_REQUEST["inCodigoNivel"]);
        $this->obMontaAtividade->setValorReduzido($_REQUEST["stChaveAtividade"]);
        $this->obMontaAtividade->setMascara($_REQUEST['stMascara']);
        $this->obMontaAtividade->preencheCombosAtividade();
    }

    public function limparSession($arParam)
    {
        Sessao::write('arDocumentos', array());
    }
}
