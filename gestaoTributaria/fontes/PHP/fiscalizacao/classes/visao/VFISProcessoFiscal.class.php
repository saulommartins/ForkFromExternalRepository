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
include_once CAM_GT_MON_COMPONENTES."IPopUpCredito.class.php";
include_once CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONCredito.class.php";
include_once CAM_GT_FIS_MAPEAMENTO."TFISFiscalFiscalizacao.class.php";

final class VFISProcessoFiscal
{
    private $controller;
    private $pgDestino;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function setPagina($valor)
    {
        $this->pgDestino = $valor;
    }

    public function getPagina()
    {
        return $this->pgDestino;
    }

    public function IncluirProcessoFiscal($param)
    {
        return $this->controller->Incluir("TFISProcessoFiscal",$param);
    }

    public function alterarProcessoFiscal($param)
    {
        return $this->controller->Alterar($param);
    }

    public function cancelarProcessoFiscal($param)
    {
        return $this->controller->Cancelar($param);
    }

    public function encerrarProcessoFiscal($param)
    {
        return $this->controller->Encerrar($param);
    }

    public function infracoesProcesso($inCodProcesso)
    {
        $this->controller->setCriterio(null);

        return $this->controller->getInfracoes();
    }

    public function BuscaDadosProcesso($inCodProcesso)
    {
        $this->controller->setCriterio("cod_processo = ".$inCodProcesso);

        return  $this->controller->getProcessoFiscal();
    }

    public function BuscaInscricaoEconomicaProcesso($inCodProcesso)
    {
        $this->controller->setCriterio("pf.cod_processo = ".$inCodProcesso);
        $rsProcessoFiscal = $this->controller->getInscricaoEconomicaProcesso();

        $this->controller->setCriterio(" and CE.inscricao_economica = ".$rsProcessoFiscal->arElementos[0]["inscricao"]);
        $rsInscricaoEconomicaProcesso = $this->controller->getInscricao('economica');

        return $rsInscricaoEconomicaProcesso;
    }

    public function BuscaInscricaoEconomicaProcessoAlteracao($inCodProcesso)
    {
        $this->controller->setCriterio("pf.cod_processo = ".$inCodProcesso);
        $rsProcessoFiscal = $this->controller->getInscricaoEconomicaProcessoAlteracao();

        $this->controller->setCriterio(" and CE.inscricao_economica = ". $rsProcessoFiscal->arElementos[0]["inscricao"] );
        $rsInscricaoEconomicaProcesso = $this->controller->getInscricao('economica');

        return $rsInscricaoEconomicaProcesso;
    }

    public function BuscaInscricaoImobiliariaProcesso($inCodProcesso)
    {
        $this->controller->setCriterio("pf.cod_processo = ".$inCodProcesso);
        $rsProcessoFiscal = $this->controller->getInscricaoImobiliariaProcesso();

        $this->controller->setCriterio(" AND I.inscricao_municipal = " .$rsProcessoFiscal->arElementos[0]["inscricao"] );
        $rsInscricaoImobiliariaProcesso = $this->controller->getInscricao('imobiliaria');

        return $rsInscricaoImobiliariaProcesso;
    }

    public function verificaAtribuicaoFiscal($param)
    {
        $this->controller->setCriterio("fc.numcgm = ".Sessao::read('numCgm'));
        $rsFiscal = $this->controller->getFiscais();
        $this->controller->setCriterio("cod_fiscal = ".$rsFiscal->getCampo('codigo'));
        $rsFiscalFiscalizacao = $this->controller->getFiscalFiscalizacao();

        $cont = 0;

        foreach ($rsFiscalFiscalizacao->arElementos as $fiscalizacao) {
            if ($param["inTipoFiscalizacao"] == $fiscalizacao["cod_tipo"] or $rsFiscal->getCampo('adm') == 't' or $param["inTipoFiscalizacao"] =='') {
                $cont = 1;
            }
        }

        if ($cont == 0) {
            $stJS = "if (document.getElementById('inTipoFiscalizacao')) {";
            $stJS.= "   document.getElementById('inTipoFiscalizacao').value = '';";
            $stJS.= "} else if (document.getElementById('txtTipoFiscalizacao')) {";
            $stJS.= "   document.getElementById('txtTipoFiscalizacao').value= '';";
            $stJS.= "   document.getElementById('cmbTipoFiscalizacao').value= '';";
            $stJS.= "}";

            $stJS .= "alertaAviso('Erro ao cadastrar processo fiscal!(Fiscal não atribuido para esse tipo de fiscalizacao)','form','erro','<?=Sessao::getId()?>');";

            return $stJS;
        }
    }

    public function getFiscalAtivo()
    {
        $this->controller->setCriterio("numcgm = ".Sessao::read('numCgm')."and ativo = 't'");
        $rsFiscalFiscalizacao = $this->controller->getFiscalAtivo();
        if ($rsFiscalFiscalizacao->Eof()) {
            return false;
        } else {
            return true;
        }

    }

    public function getFiscalLogado()
    {
        $this->controller->setCriterio("numcgm = ".Sessao::read('numCgm'));

        return $this->controller->getFiscalAtivo();
    }

    public function BuscaFiscalProcesso($inCodProcesso)
    {
        $this->controller->setCriterio("pf.cod_processo = ".$inCodProcesso);
        $rsFiscais = $this->controller->recuperaFiscalProcesso();

        $opt = array(
                "cabecalho"=>"Lista de Fiscais",
                "span"=>"spnListaFiscal",
                "desc"=>"",
                "alvo"=>"",
                "codigo"=>"",
                "container"=>"arFiscal"
                );

        $arValores = Sessao::read($opt['container']);

        if ( count( $rsFiscais->arElementos ) ) {
            for ( $i = 0; $i < count( $rsFiscais->arElementos ); $i++ ) {
                $arValores[$i]['codigo' ] 	= $rsFiscais->arElementos[$i]['codigo'];
                $arValores[$i]['nome'] 	= $rsFiscais->arElementos[$i]['descricao'];

                $Hdn = $this->GeraHidden("fiscal", $rsFiscais->arElementos[$i]['codigo']);
                $arValores[$i]['hidden'] = $Hdn;
            }
        }
        Sessao::write( $opt['container'], $arValores );

        $obLista = $this->montaLista( $arValores, false, $opt );

        return $obLista;
    }

    public function BuscaGrupoCreditoProcesso($inCodProcesso)
    {
        $this->controller->setCriterio("pf.cod_processo = ".$inCodProcesso);
        $rsGrupo = $this->controller->recuperaGrupoProcesso();

        $opt = array(
                "cabecalho"=>"Lista de Créditos / Grupos de Créditos",
                "span"=>"spnListaCreditoGrupo",
                "desc"=>"",
                "alvo"=>"",
                "codigo"=>"",
                "container"=>"arCredito"
                );

        if ( count( $rsGrupo->arElementos ) ) {
            $obRARRGrupo = new RARRGrupo;

            $stMascara = "";
            $obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascara );
            $inTamanhoMascara = strlen( $stMascara );
            $stMascara .= "/9999";

            for ( $i = 0; $i < count( $rsGrupo->arElementos ); $i++ ) {
                $grupo      = sprintf("%0".$inTamanhoMascara."d", $rsGrupo->arElementos[$i]['cod_grupo']);
                $exercicio	= $rsGrupo->arElementos[$i]['ano_exercicio'];

                $arGrupo[$i]['codigo']  = $grupo."/".$exercicio;
                $arGrupo[$i]['nome'] 	= $rsGrupo->arElementos[$i]['descricao'];

                $Hdn = $this->GeraHidden("grupo", $arGrupo[$i]['codigo']);
                $arGrupo[$i]['hidden']  = $Hdn;
            }
            $arValores  = (array) $arGrupo;
            $stTipo     = 'arGrupo';
        }

        $this->controller->setCriterio("pf.cod_processo = ".$inCodProcesso);
        $rsCredito = $this->controller->recuperaCreditoProcesso();

        if ( count( $rsCredito->arElementos ) ) {
            $obRMONCredito = new RMONCredito;
            $obRMONCredito->consultarMascaraCredito();
            $stMascaraCredito = $obRMONCredito->getMascaraCredito();
            $arMascaraCredito = explode(".", $stMascaraCredito);

            for ($inX = 0; $inX < 4; $inX++) {
                $arMascaraCredito[$inX] = strlen($arMascaraCredito[$inX]);
            }

            for ($j = 0; $j < count( $rsCredito->arElementos ); $j++) {
                $credito    = sprintf("%0".$arMascaraCredito[0]."d", $rsCredito->arElementos[$j]['cod_credito']);
                $especie    = sprintf("%0".$arMascaraCredito[1]."d", $rsCredito->arElementos[$j]['cod_especie']);
                $genero     = sprintf("%0".$arMascaraCredito[2]."d", $rsCredito->arElementos[$j]['cod_genero']);
                $natureza   = sprintf("%0".$arMascaraCredito[3]."d", $rsCredito->arElementos[$j]['cod_natureza']);

                $arCredito[$j]['codigo'] 	= $credito.'.'.$especie.'.'.$genero.'.'.$natureza;
                $arCredito[$j]['nome'] 	    = $rsCredito->arElementos[$j]['descricao_credito'];

                $Hdn = $this->GeraHidden("credito", $arCredito[$j]['codigo']);
                $arCredito[$j]['hidden'] = $Hdn;
            }
            $arValores  = (array) $arCredito;
            $stTipo     = 'arCredito';
        }

        Sessao::write('stTipo', $stTipo);
        Sessao::write($opt['container'], $arValores);

        if(is_array($arValores))
            $obLista = $this->montaLista( $arValores, false, $opt );

        return $obLista;
    }

    public function processoIniciado($inCodProcesso)
    {
        $iniciado = $this->controller->verificaProcessoIniciado($inCodProcesso);
        if ($iniciado) {
            return true;
        } else {
            return false;
        }
    }

    public function IncluirFiscal($param)
    {
        $opt = array(
                "cabecalho"=>"Lista de Fiscais",
                "span"=>"spnListaFiscal",
                "desc"=>"stFiscal",
                "alvo"=>"inFiscal",
                "codigo"=>$param["inFiscal"],
                "container"=>"arFiscal"
                );

        $arValores = Sessao::read($opt['container']);

        if ($this->PodeIncluirItemLista($opt)) {
            $this->controller->setCriterio(" fc.cod_fiscal = ".$param["inFiscal"]);
            $obFiscal = $this->controller->getFiscais();

            $js = " parent.window.document.frm.inFiscal.value='';                          						\n";
            $js.= " parent.window.document.getElementById('stFiscal').innerHTML = '&nbsp'; 	\n";

            if (count($obFiscal->arElementos) > 0) {
                $k = count( $arValores );
                $arValores[$k]['codigo' ] 	= $obFiscal->arElementos[0]['codigo'];
                $arValores[$k]['nome'] 	= $obFiscal->arElementos[0]['nome'];

                $Hdn = $this->GeraHidden("fiscal",$obFiscal->arElementos[0]['codigo']);
                $arValores[$k]['hidden'] = $Hdn;

                Sessao::write( $opt['container'], $arValores );

                $lista = $this->montaLista( $arValores, true, $opt );

                $result = $lista;
             } else {
                $stMensagem	= "@Código do Fiscal inválido (".$param["inFiscal"].")   ";
                $js        		   .= "alertaAviso(".$stMensagem."','form','erro','".Sessao::getId()."');\n";
                $result 			= $js;
            }
        } else {
            $stMensagem = "@Fiscal já informado.(".$param["inFiscal"].")   ";
            $js        .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');          \n";
            $result = $js;
        }

        return $result;
    }

    public function IncluirCredito($param)
    {
        $opt = array(
                "cabecalho"=>"Lista de Créditos / Grupos de Créditos",
                "span"=>"spnListaCreditoGrupo",
                "desc"=>"stCredito",
                "alvo"=>"inCodCredito",
                "codigo"=>$param["inCodCredito"],
                "container"=>"arCredito"
                 );

        $arValores = Sessao::read($opt["container"]);

        if ($this->PodeIncluirItemLista($opt)) {
            $codigo = explode(".",$param["inCodCredito"]);

            $credito 		= intval($codigo[0]);
            $especie 		= intval($codigo[1]);
            $genero 		= intval($codigo[2]);
            $natureza 	= intval($codigo[3]);
            $chaveCompostaCredito = $credito.$especie.$genero.$natureza;

            $this->controller->setCriterio("mc.cod_credito::varchar||me.cod_especie::varchar||mg.cod_genero::varchar||mn.cod_natureza::varchar  = ".$chaveCompostaCredito . '::varchar');

            $obCredito = $this->controller->getCreditos();

            $js = " parent.window.document.frm.inCodCredito.value='';                          	     \n";
            $js.= " parent.window.document.getElementById('stCredito').innerHTML = '&nbsp'; 	     \n";

            if ($obCredito) {
                $k = count( $arValores );
                $arValores[$k]['codigo' ] = $param["inCodCredito"];
                $arValores[$k]['nome'] = $obCredito->arElementos[0]['descricao_credito'];
                $Hdn = $this->GeraHidden("credito",$param["inCodCredito"]);
                $arValores[$k]['hidden'] = $Hdn;

                Sessao::write( $opt["container"], $arValores );

                $result = $this->montaLista( $arValores, true, $opt );
             } else {
                $stMensagem = "@Código do Crédito inválido (".$param["inCodCredito"].")   ";
                $js        .= "alertaAviso(".$stMensagefc."','form','erro','".Sessao::getId()."');   \n";
                $result = $js;
            }
        } else {
            $stMensagem = "@Crédito já informado.(".$param["inCodCredito"].")   ";
            $js        .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');       \n";
            $result = $js;
        }

        return $result;
    }

    public function InicializaFiscal($codigo,$nome,$campo)
    {
        $dados = array();
        $hdn = $this->GeraHidden($campo, $codigo);
        $ar = array(
                "codigo" => $codigo,
                "nome" => $nome,
                "hidden"=>$hdn
               );
        array_push($dados,$ar);
        $opt = array(
                "cabecalho"=>"Lista de Fiscais",
                "span"=>"spnListaFiscal",
                "desc"=>"stFiscal",
                "alvo"=>"inFiscal",
                "codigo"=>$dados["codigo"],
                "container"=>"arFiscal"
                );
        $rsRecordSet = new RecordSet();
        $rsRecordSet->preenche( $dados );

        $this->controller->setCriterio("fc.numcgm = ".Sessao::read('numCgm'));
        $rsFiscal = $this->controller->getFiscais();

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( $opt["cabecalho"]);

        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth   ( 5        );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth   ( 10       );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nome" );
        $obLista->ultimoCabecalho->setWidth   ( 80          );
        $obLista->commitCabecalho();

        if ($rsFiscal->getCampo('adm') == 't') {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Ação" );
            $obLista->ultimoCabecalho->setWidth   ( 10     );
            $obLista->commitCabecalho();
        }

        ////dados

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento( "CENTRO"   );
        $obLista->ultimoDado->setCampo      ( "codigo" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA"  );
        $obLista->ultimoDado->setCampo      ( "[nome] [hidden]" );
        $obLista->commitDado();

        if ($rsFiscal->getCampo('adm') == 't') {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao  ( "EXCLUIR"                                                 );
            $obLista->ultimaAcao->setFuncao( true                                                      );
            $obLista->ultimaAcao->setLink  ( "javascript: executaFuncaoAjax('ExcluirItemLista');" );
            $obLista->ultimaAcao->addCampo ( "","&inId=[codigo]&span={$opt['span']}&cabecalho={$opt['cabecalho']}&desc={$opt['desc']}&alvo={$opt['alvo']}&container={$opt['container']}" );
            $obLista->commitAcao();
        }

        $obLista->montaHTML();

        $html = $obLista->getHTML();
        $html = str_replace( "\n","",$html   );
        $html = str_replace( "  ","",$html   );
        //$html = str_replace( "'","\\'",$html );
        return $html;
    }

    public function VerificaFiscal($numcgm)
    {
        $numcgm = preg_split( "/:/",$numcgm);
        $numcgm = $numcgm[2];
        $numcgm = str_replace("\"","",$numcgm);
        $numcgm = str_replace(";","",$numcgm);
        $numcgm = intval($numcgm);
        $fiscal = $this->controller->isFiscal($numcgm)->arElementos;

         if ($fiscal) {
            $dados = array(
                "codigo"=>$fiscal[0]["cod_fiscal"],
                "nome"=>$fiscal[0]["nom_cgm"],
                "administrador"=>$fiscal[0]["administrador"],
                "status"=>$fiscal[0]["ativo"]
            );

            Sessao::write("fiscal", $dados);

            return $fiscal;
        } else {
            return false;
        }
    }

    public function verificaDocumentos($param)
    {
        $dados = $param;
        $tipoFiscalizacao = $param["tipoFiscalizacao"];
        $documentos = $this->controller->getDocumentos(" and cod_tipo_fiscalizacao = ".$tipoFiscalizacao);
        $return = "";
        if (!$documentos->arElementos) {

            $stMensagem = "@Não há documento de uso interno cadastrado para este tipo de fiscalização (".$tipoFiscalizacao.")   ";
                $js        .= "alertaAviso(".$stMensagem."','form','erro','".Sessao::getId()."');\n";
            $result = $js;

        }

        return $result;
    }

    public function LimparGrupoCredito()
    {
        $opt = array(
                "cabecalho"=>"Lista de Créditos / Grupos de Créditos",
                "span"=>"spnListaCreditoGrupo",
                "desc"=>"stGrupo",
                "alvo"=>"inCodGrupo",
                "codigo"=>$param["inCodGrupo"],
                "container"=>"arCredito"
                 );

        $js = " document.frm.inCodCredito.value='';                                    \n";
        $js.= " document.getElementById('spnVinculo').innerHTML = '';                  \n";
        $js.= " document.getElementById('spnListaCreditoGrupo').innerHTML = '';        \n";

        Sessao::write( $opt["container"], null );

        return $js;
    }

    public function LimparGrupo()
    {
        $js = " document.frm.inCodGrupo.value='';               \n";
        $js.= " document.getElementById('stGrupo').innerHTML = '&nbsp;';\n";

        return $js;
    }

    public function IncluirGrupoCredito($param)
    {
        $opt = array(
                "cabecalho"=>"Lista de Créditos / Grupos de Créditos",
                "span"=>"spnListaCreditoGrupo",
                "desc"=>"stGrupo",
                "alvo"=>"inCodGrupo",
                "codigo"=>$param["inCodGrupo"],
                "container"=>"arCredito"
                 );

        $arValores = Sessao::read($opt["container"]);

        if ($this->PodeIncluirItemLista($opt)) {
            $codigo = explode("/",$param["inCodGrupo"]);
            $grupo = intval($codigo[0]);
            $exercicio = intval($codigo[1]);
            $chaveCompostaGrupo = $grupo.$exercicio;

            $stFiltro = "\r\n\t acg.cod_grupo = ".$grupo." AND \r\n\t acg.ano_exercicio = '".$exercicio."'";

            $this->controller->setCriterio($stFiltro);
            $obGrupoCredito = $this->controller->getGrupoCreditos();

               $js = " document.frm.inCodCredito.value='';                               \n";
            $js.= " document.getElementById('stCredito').innerHTML = '&nbsp';         \n";

            if ($obGrupoCredito) {
                $dados = $obGrupoCredito->arElementos[0];
                $k = count( $arValores );
                $codGrupo = $codigo[0]."/".$dados["ano_exercicio"];
                $arValores[$k]['codigo' ] = $codGrupo;
                $arValores[$k]['nome'] = $dados["descricao"];
                $Hdn = $this->GeraHidden("grupo",$codGrupo);
                $arValores[$k]['hidden'] = $Hdn;

                Sessao::write( $opt["container"], $arValores );

                $result = $this->montaLista( $arValores, true, $opt );
             } else {
                $stMensagem = "@Grupo de Crédito inválido (".$param["inCodGrupo"].")   ";
                $js.= "alertaAviso(".$stMensagefc."','form','erro','".Sessao::getId()."');          \n";
                $result = $js;
            }
        } else {
            $stMensagem = "@Grupo de Crédito já informado.(".$param["inCodGrupo"].")   ";
            $js.= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');              \n";
            $result = $js;
        }

        return $result;
    }

    public function PodeIncluirItemLista($param)
    {
        $boLista = true;
        $arValores = Sessao::read($param['container']);

        if (is_array($arValores)) {
            foreach ($arValores as $key=>$value) {
                if ($value["codigo"] == $param['codigo']) {
                    $boLista = false;
                }
            }
        }

        return $boLista;
    }

    public function ExcluirItemLista($param)
    {
        $arRetorno = array();
        $k = 0;
        $key = trim($param["inId"]);
        $arValores = Sessao::read($param['container']);

        if (is_array($arValores)) {
            foreach ($arValores as $value) {
                $keyValue = trim($value['codigo']);
                if ($key !== $keyValue) {
                    $arRetorno[$k]['codigo' ] = $value['codigo' ];
                    $arRetorno[$k]['nome'] = $value['nome'];
                $arRetorno[$k]['hidden'] = $value['hidden'];
                $k++;
                }
            }
        }

        Sessao::write( $param['container'], $arRetorno );

        return $this->montaLista( $arRetorno, true, $param );
    }

    public function GeraHidden($nome, $value)
    {
        $obHdn =  new Hidden;
        $obHdn->setName ( "{$nome}[]");
        $obHdn->setValue( $value );
        $obHdn->montaHtml();

        return $obHdn->getHtml();
    }

    public function montaLista($arValores, $stAcao = '',$opt)
    {
        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche( $arValores );
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( $opt["cabecalho"]);

        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth   ( 5        );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth   ( 10       );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        if ($opt[span] == 'spnListaFiscal') {
            $obLista->ultimoCabecalho->addConteudo( "Fiscal" );
        } else {
            $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        }
        $obLista->ultimoCabecalho->setWidth   ( 80          );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Ação" );
        $obLista->ultimoCabecalho->setWidth   ( 10     );
        $obLista->commitCabecalho();

        ////dados

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento( "CENTRO"   );
        $obLista->ultimoDado->setCampo      ( "codigo" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA"  );
        $obLista->ultimoDado->setCampo      ( "[nome] [hidden]" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao  ( "EXCLUIR"                                               	);
        $obLista->ultimaAcao->setFuncao( true                                                      	);
        $obLista->ultimaAcao->setLink  ( "javascript: executaFuncaoAjax('ExcluirItemLista');" );
        $obLista->ultimaAcao->addCampo ( "","&inId=[codigo]&span={$opt['span']}&cabecalho={$opt['cabecalho']}&desc={$opt['desc']}&alvo={$opt['alvo']}&container={$opt['container']}"	);
        $obLista->commitAcao();

        $obLista->montaHTML();

        $html = $obLista->getHTML();

        if ($stAcao) {
            $html = str_replace( "\n","",$html   );
            $html = str_replace( "  ","",$html   );
            $html = str_replace( "'","\\'",$html );

            $stJs = " d.getElementById('{$opt['span']}').innerHTML	= '';          		\n";

            if ($opt['alvo'])
                $stJs.= " document.frm.{$opt['alvo']}.value = '';		       						\n";
            if ($opt['desc'])
                $stJs.= " d.getElementById('{$opt['desc']}').innerHTML	= '&nbsp';	    \n";

            $stJs.= " d.getElementById('{$opt['span']}').innerHTML	= '".$html."'; 		\n";

            $result  = $stJs;
        } else {
            $result  = $html;
        }

        return $result;
    }

    public function BarraBtnIncluirLimpar($fIncluir,$fLimpar)
    {
        $obBtnIncluir = new Button;
        $obBtnIncluir->setName             ( "btn{$fIncluir}"             );
        $obBtnIncluir->setValue            ( "Incluir"                );
        $obBtnIncluir->setTipo             ( "button"                 );
        $obBtnIncluir->obEvento->setOnClick( "{$fIncluir}();" );
        $obBtnIncluir->setDisabled         ( false                    );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName             ( "btn{$fLimpar}" );
        $obBtnLimpar->setValue            ( "Limpar"    );
        $obBtnLimpar->setTipo             ( "button"    );
        $obBtnLimpar->obEvento->setOnClick( "{$fLimpar}();" );
        $obBtnLimpar->setDisabled         ( false       );

        $botoesSpan = array( $obBtnIncluir, $obBtnLimpar );

        return $botoesSpan;
    }

    public function MostraCredito()
    {
        $obFormulario = new Formulario;
        $obCredito = new IPopUpCredito;
        $obCredito->setNull(false);
        $obCredito->geraFormulario($obFormulario);
        $obBarra = $this->BarraBtnIncluirLimpar("IncluirCredito","LimparCredito");
        $obFormulario->defineBarra( $obBarra,'left','' );
        $obFormulario->montaInnerHTML();
        $retorno = "$('spnVinculo').innerHTML = '".$obFormulario->getHTML()."';";

        return $retorno;
    }

    public function MostraGrupoCredito()
    {
        $obFormulario = new Formulario;
        $obGrupoCredito = new MontaGrupoCredito;
        $obGrupoCredito->geraFormulario($obFormulario);
        $obBarra = $this->BarraBtnIncluirLimpar("IncluirGrupoCredito","LimparGrupo");
        $obFormulario->defineBarra( $obBarra,'left','' );
        $obFormulario->montaInnerHTML();
        $retorno = "$('spnVinculo').innerHTML = '".$obFormulario->getHTML()."';";

        return $retorno;
    }

    public function defineSessaoLista($stNomeSessao,$arValorSessao,$stHdnNome,$stHdnCodigo)
    {
        $Hdn = $this->GeraHidden($stHdnNome,$stHdnCodigo);
        Sessao::write( $stNomeSessao, $arValorSessao );
    }

    public function montaDadosSessaoLista($NomeArray,$nome,$codigo,$hidden)
    {
        $NomeArray = array();
        $k = count( $NomeArray );
        $NomeArray[$k]['codigo' ]   = $codigo;
        $NomeArray[$k]['nome']      = $nome;
        $NomeArray[$k]['hidden']    = $hidden;

        return $NomeArray;
    }

    public function getTipoFiscalizacao($codigo)
    {
        $this->controller->setCriterio(" tipo_fiscalizacao.cod_tipo = {$codigo}");

        return $this->controller->getTipoFiscalizacao();
    }

    // busca dado de fundamentação legal com base em cod_processo.
    public function getFundamentacao($inCodProcesso, $inTipoFiscalizacao)
    {
        $this->controller->setCriterio("pf.cod_processo = ".$inCodProcesso);
        if ($inTipoFiscalizacao == 1) {
            return $this->controller->getFundamentacaoTributaria();
        } else {
            return $this->controller->getFundamentacaoObras();
        }
    }

 }
