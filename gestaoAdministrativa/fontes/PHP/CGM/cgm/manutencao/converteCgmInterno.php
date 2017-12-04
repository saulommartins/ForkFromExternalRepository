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
* Arquivo de instância para manutenção de CGM
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 8144 $
$Name$
$Author: cassiano $
$Date: 2006-03-31 17:10:58 -0300 (Sex, 31 Mar 2006) $

* Casos de uso: uc-01.02.92, uc-01.02.93
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_FW_LEGADO."funcoesLegado.lib.php";
include CAM_FW_LEGADO."cgmLegado.class.php"; //Insere a classe que manipula os dados do CGM
include CAM_FW_LEGADO."auditoriaLegada.class.php"; //Inclui classe para inserir auditoria
include 'interfaceCgm.class.php'; //Insere a classe que constroi a interface html para CGM

if(!isset($controle))
    $controle = 0;
?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>
<?php
$html = new interfaceCgm;
$objCgm = new cgmLegado;

switch ($controle) {
    case 0: /** Monta o formulário de busca **/
        $html->formBuscaCgmConverte();
        break;
    case 1: //O valor 1 da variável $controle está reservado para montar o formulário do CGM
        //Popular o array com os dados recebidos do form
        if ($paisCorresp == "xxx") {
            $paisCorresp = 0;
        }
        if ($estadoCorresp == "xxx") {
            $estadoCorresp = 0;
        }
        if ($municipioCorresp == "xxx" or empty($municipioCorresp)) {
            $municipioCorresp = 0;
        }
        if ( strtolower($catHabilitacao) == "xxx" ) {
            $catHabilitacao = '0';
        }
        $dadosCgm = array(
            numCgm=>$numCgm,
            codMunicipio=>$codMunicipio,
            codUf=>$codUF,
            codMunicipioCorresp=>$codMunicipioCorresp,
            codUfCorresp=>$codUfCorresp,
            nomCgm=>$nomCgm,
            tipoLogradouro=>$tipoLogradouro,
            logradouro=>$logradouro,
            numero=>$numero,
            complemento=>$complemento,
            pais=>$pais,
            estado=>$estado,
            municipio=>$municipio,
            bairro=>$bairro,
            //cep=>$cep1.$cep2,
            cep=>preg_replace('/[^a-zA-Z0-9]/','',$cep),
            tipoLogradouroCorresp=>$tipoLogradouroCorresp,
            logradouroCorresp=>$logradouroCorresp,
            numeroCorresp=>$numeroCorresp,
            complementoCorresp=>$complementoCorresp,
            paisCorresp=>$paisCorresp,
            estadoCorresp=>$estadoCorresp,
            municipioCorresp=>$municipioCorresp,
            bairroCorresp=>$bairroCorresp,
            //cepCorresp=>$cepCorresp1.$cepCorresp2,
            cepCorresp=>preg_replace('/[^a-zA-Z0-9]/','',$cepCorresp),
            foneRes=>$dddRes.$foneRes,
            ramalRes=>$ramalRes,
            foneCom=>$dddCom.$foneCom,
            ramalCom=>$ramalCom,
            foneCel=>$dddCel.$foneCel,
            email=>$email,
            emailAdic=>$emailAdic,
            codResp=>Sessao::read('numCgm'),
            pessoa=>$pessoa,
            //cnpj=>$cnpj1.$cnpj2.$cnpj3.$cnpj4.$cnpj5,
            cnpj=>preg_replace('/[^a-zA-Z0-9]/','', $cnpj ),
            inscEst=>$inscEstadual,
            //cpf=>$cpf1.$cpf2.$cpf3.$cpf4,
            cpf=>preg_replace('/[^a-zA-Z0-9]/','', $cpf ),
            rg=>$rg,
            orgaoEmissor=>$orgaoEmissor,
            //dtEmissaoRg=>$dtEmissaoRg3."-".$dtEmissaoRg2."-".$dtEmissaoRg1,
            dtEmissaoRg=>$dtEmissaoRg,
            numCnh=>$numCnh,
            //dtValidadeCnh=>$dtValidadeCnh3."-".$dtValidadeCnh2."-".$dtValidadeCnh1,
            dtValidadeCnh=>$dtValidadeCnh,
            nomFantasia=>$nomFantasia,
            catHabilitacao=>$catHabilitacao,
            cod_escolaridade=>$cod_escolaridade,
            );
        //$cpfigual = $cpf1.$cpf2.$cpf3.$cpf4;
        //$cnpjigual = $cnpj1.$cnpj2.$cnpj3.$cnpj4.$cnpj5;
        $cpfigual = $cpf;
        $cnpjigual = $cnpj;

        $objCgm = new cgmLegado;
        if ($atributo) {
            $objCgm->setAtributo( $atributo );
        }

        $cpfigual = preg_replace( '/[^a-zA-Z0-9]/','', $cpf );
        $cnpjigual = preg_replace( '/[^a-zA-Z0-9]/','', $cnpj );

if ($pessoa == "fisica") {
            if (comparaValor("cpf", $cpfigual, "sw_cgm_pessoa_fisica", "and numcgm <> $dadosCgm[numCgm]")) {
                if ($objCgm->alteraCgmConverte($dadosCgm)) {
                    //Insere auditoria
                    //echo "1";
                    //die();
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $dadosCgm[numCgm]);
                    $audicao->insereAuditoria();
                    alertaAviso($PHP_SELF."?controle=0&pagina=".$pagina."&volta=true","CGM $dadosCgm[numCgm]","alterar","aviso");
                } else {
                //echo "2";
                //die();
                    $stMensagem = $objCgm->stErro;
                    //sessao->transf2 = $dadosCgm;
                    Sessao::write('dadosCgm', $dadosCgm);
                    alertaAviso($PHP_SELF."?controle=7&numCgm=".$dadosCgm[numCgm]."&pessoa=".$pessoa,"CGM $dadosCgm[numCgm] $stMensagem","n_alterar","erro");
                }
            } else {
                echo '
                    <script type="text/javascript">
                    alertaAviso("O cpf '.$cpfigual.' já existe","unica","erro","'.Sessao::getId().'");
                    </script>';
                    $html->formCgm($dadosCgm,$PHP_SELF,0);
            }
        } elseif ($pessoa == "juridica") {
            if (comparaValor("cnpj", $cnpjigual, "sw_cgm_pessoa_juridica", "and numcgm <> $dadosCgm[numCgm]")) {
                if ($objCgm->alteraCgmConverte($dadosCgm)) {
                    //Insere auditoria
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $dadosCgm[numCgm]);
                    $audicao->insereAuditoria();
                    alertaAviso($PHP_SELF."?controle=0&pagina=".$pagina."&volta=true","CGM $dadosCgm[numCgm]","alterar","aviso");
                } else {
                    $stMensagem = $objCgm->stErro;
                    //sessao->transf2 = $dadosCgm;
                    Sessao::write('dadosCgm', $dadosCgm);
                    alertaAviso($PHP_SELF."?controle=7&numCgm=".$dadosCgm[numCgm]."&pessoa=".$pessoa,"CGM $dadosCgm[numCgm] $stMensagem","n_alterar","erro");
                }
            } else {
                echo '
                    <script type="text/javascript">
                    alertaAviso("O cnpj '.$cnpjigual.' já existe","unica","erro","'.Sessao::getId().'");
                    </script>';
                $html->formCgm($dadosCgm,$PHP_SELF,0);
            }
        } elseif ($pessoa == "geral") {
            if ($objCgm->alteraCgm($dadosCgm)) {
                //Insere auditoria
                $audicao = new auditoriaLegada;
                $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $dadosCgm[numCgm]);
                $audicao->insereAuditoria();
                alertaAviso($PHP_SELF."?pessoa=".$pessoa."&controle=3&pagina=".$pagina."&volta=true","CGM ".$dadosCgm[numCgm],"alterar","aviso");
            } else {
                $stMensagem = $objCgm->stErro;
                //sessao->transf2 = $dadosCgm;
                Sessao::write('dadosCgm', $dadosCgm);
                alertaAviso($PHP_SELF."?controle=7&numCgm=".$dadosCgm[numCgm]."&pessoa=".$pessoa,"CGM $dadosCgm[numCgm] $stMensagem","n_alterar","erro");
            }
        }
    break;
    case 2: /** Exibe uma lista com o resultado da busca **/
        //** Monta um vetor com os dados recebidos do formulário de busca **/
        if ($_GET['volta'] == 'true' or $_GET['paginando'] == 'true') {
            //dadosBusca = sessao->transf3;
            $dadosBusca = Sessao::read('dadosBusca');
        } else {
            $dadosBusca = array(
            numCgm=>$numCgm,
            nomCgm=>$nomCgm,
            tipoBusca=>$tipoBusca);
            //sessao->transf3 = $dadosBusca;
            Sessao::write('dadosBusca', $dadosBusca);
        }

        $objCgm = new cgmLegado;
        $html = new interfaceCgm;
        $html->exibeBusca($objCgm->montaPesquisaCgmConverteCgmInterno($dadosBusca),'alterar');
        //** Envia o vetor com a busca e recebe uma matriz com o resultado **/
    break;
    case 3:
        //Seleciona o tipo de cadastro
        if ($pessoa != "outros") {
        ?>
        <form name='frm2' action='<?=$PHP_SELF;?>?<?=Sessao::getId();?>' method='post'>
            <input type="hidden" name="controle" value="">
            <input type="hidden" name="numCgm" value="<?=$numCgm?>" >
                <table width='100%'>
                    <tr>
                        <td class=alt_dados colspan=2>Tipo de Cadastro</td>
                    </tr>
                    <tr>
                        <td class="label" width="30%" title="Tipo de cadastro: Pessoa Física ou Jurídica">*Tipo de cadastro</td>
                        <td class="field" width="70%">
        <?php echo $html->comboTipoCgm("pessoa",$pessoa,"onChange='document.frm2.controle.value=6;document.frm2.submit();'");  ?>
                    </td>
                </tr>
            </table>
            </form>
        <?php
        }
        if (isset($pessoa)) {
            $dados = $_POST;
            $dados[pessoa] = $pessoa;
            $html->formCgm($dados,$PHP_SELF,0);
        }
    break;
    case 4:
            if ($campoMunicipio == "municipio") {
                $uf = $estado;
                $nomeuf = "estado";
                $m = "municipio";
            } else {
                $uf = $estadoCorresp;
                $nomeuf = "estadoCorresp";
                $m = "municipioCorresp";
            }
            if ($uf != 'xxx') {
                $sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$uf." ORDER by nom_municipio";
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                $comboMunicipio = "";
                $iCont = 1;
                $js .= "limpaSelect(f.".$campoMunicipio.",1); \n";

                while (!$dbEmp->eof()) {
                    $codg_municipio  = trim($dbEmp->pegaCampo("cod_municipio"));
                    $nomg_municipio  = trim($dbEmp->pegaCampo("nom_municipio"));
                    $dbEmp->vaiProximo();
                    $js .= "f.".$campoMunicipio.".options[".$iCont++."] = new Option('".$nomg_municipio."','".$codg_municipio."');\n";
                }
                if ($iCont > 1) {
                    $js .= "f.".$campoMunicipio.".focus();\n";
                }
                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
            } else {
            $js .= "limpaSelect(f.".$m.",1);\n";
            }
            executaFrameOculto($js);
    break;

    case 5:
        if ($campoMunicipio == "municipio") {
            $uf = $estado;
            $nomeuf = "estado";
            $m = "municipio";
        } else {
            $uf = $estadoCorresp;
            $nomeuf = "estadoCorresp";
            $m = "municipioCorresp";
        }
        if ($uf != 'xxx') {
            $sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$uf." ORDER by nom_municipio";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $comboMunicipio = "";
            $iCont = 1;
            $js .= "limpaSelect(f.".$campoMunicipio.",1); \n";
            while (!$dbEmp->eof()) {
                $codg_municipio  = trim($dbEmp->pegaCampo("cod_municipio"));
                $nomg_municipio  = trim($dbEmp->pegaCampo("nom_municipio"));
                $dbEmp->vaiProximo();
                $js .= "f.".$campoMunicipio.".options[".$iCont++."] = new Option('".$nomg_municipio."','".$codg_municipio."');\n";
            }
            if ($iCont > 1) {
                $js .= "f.".$campoMunicipio.".disabled = false;\n";
                $js .= "f.".$campoMunicipio.".focus();\n";
            }
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
        } else {
            $js .= "limpaSelect(f.".$m.",1);\n";
        }
        executaFrameOculto($js);
    break;
    case 2000:
            if ($campoMunicipio == "estado") {
                $estado = $pais;
                $nomeestado = "municipio";
                $e = "estado";
            } else {
                $estado = $paisCorresp;
                $nomeestado = "municipioCorresp";
                $e = "estadoCorresp";
            }
            if ($estado != 'xxx') {
                $sSQL = "SELECT * FROM sw_uf where cod_pais=".$estado." ORDER by nom_uf";
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                $comboEstado = "";
                $iCont = 1;
                $js .= "limpaSelect(f.".$campoMunicipio.",1); \n";
                $js .= "limpaSelect(f.".$nomeestado.",1); \n";
                while (!$dbEmp->eof()) {
                    $codg_estado  = trim($dbEmp->pegaCampo("cod_uf"));
                    $nomg_estado  = trim($dbEmp->pegaCampo("nom_uf"));
                    $dbEmp->vaiProximo();
                    $js .= "f.".$campoMunicipio.".options[".$iCont++."] = new Option('".$nomg_estado."','".$codg_estado."');\n";
                }
                if ($iCont > 1) {
                    $js .= "f.".$campoMunicipio.".focus();\n";
                }
                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
            } else {
                $js .= "limpaSelect(f.".$nomeestado.",1); \n";
                $js .= "limpaSelect(f.".$e.",1); \n";
            }
            executaFrameOculto($js);
    break;
    case 6: /** Monta o formulário com os dados do CGM escolhido **/
        $html->formCgm($objCgm->pegaDadosCgmConverte($numCgm,$pessoa),$PHP_SELF,0);
    break;
    case 7: /** Monta o formulário com os dados do CGM escolhido **/

        $html->formCgm(Sessao::write('dadosCgm'),$PHP_SELF,0);
        Sessao::remove('dadosCgm');

        /*$html->formCgm(sessao->transf2,$PHP_SELF,0);
        sessao->transf2 = "";*/

    break;
}//Fim switch
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
