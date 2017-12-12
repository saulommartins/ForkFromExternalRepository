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
      * Manutenção ee configuração
      * Data de Criação: 25/07/2005

      * @author Analista: Cassiano
      * @author Desenvolvedor: Cassiano

      $Id: configuracaoBasica.php 66178 2016-07-26 18:31:34Z evandro $

      Casos de uso: uc-01.03.97
    */

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );

setAjuda( "UC-01.03.97" );

$ctrl = $request->get('ctrl');

if (!isset($ctrl)) {
    $ctrl = 0;
}

switch ($ctrl) {

    case 0:

        $nom_prefeitura = $request->get('nom_prefeitura');

        $sSQL = "SELECT count(*) as regSel from administracao.configuracao where cod_modulo=2 and parametro like 'sam%' and exercicio = '".Sessao::getExercicio()."';";

        $samlinkExiste = false;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        if($dbEmp->pegaCampo("regSel")>0)
            $samlinkExiste = true;
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

        if (!(isset($nom_prefeitura))) {
            // Pega os valores atuais da tabela CONFIGURACAO
            $cod_municipio        = pegaConfiguracao("cod_municipio");
            $codUf                = pegaConfiguracao("cod_uf");
            $nomPrefeitura        = pegaConfiguracao("nom_prefeitura");
            $tipoLogradouro       = pegaConfiguracao("tipo_logradouro");
            $logradouro           = pegaConfiguracao("logradouro");
            $numero               = pegaConfiguracao("numero");
            $complemento          = pegaConfiguracao("complemento");
            $bairro               = pegaConfiguracao("bairro");
            $cep                  = pegaConfiguracao("cep");
            $ddd                  = pegaConfiguracao("ddd");
            $fone                 = pegaConfiguracao("fone");
            $fax                  = pegaConfiguracao("fax");
            $email                = pegaConfiguracao("e_mail");
            $site                 = pegaConfiguracao("site");
            $cnpj                 = pegaConfiguracao("cnpj");
            $populacao            = pegaConfiguracao("populacao");
            $prefeito             = pegaConfiguracao("CGMPrefeito");
            $diario               = pegaConfiguracao("CGMDiarioOficial");
            $dt_implantacao       = pegaConfiguracao("dt_implantacao");
            $logotipo             = pegaConfiguracao("logotipo");
            $periodoAuditoria     = pegaConfiguracao("periodo_auditoria");
            $usuarioRelatorio     = pegaConfiguracao("usuario_relatorio");
            $mensagem             = pegaConfiguracao("mensagem");
            $status               = pegaConfiguracao("status");
            $nomMunicipio         = pegaConfiguracao("nom_municipio");
            $anoExercicio         = pegaConfiguracao("ano_exercicio");
            $diretorio            = pegaConfiguracao("diretorio");
            $mascaraSetor         = pegaConfiguracao("mascara_setor");
            $mascaraLocal         = pegaConfiguracao("mascara_local");            
            $codMunicipioIBGE     = pegaConfiguracao("cod_municipio_ibge");

            $samlinkHost 	= pegaConfiguracao("samlink_host");
            $samlinkPort 	= pegaConfiguracao("samlink_port");
            $samlinkDBName 	= pegaConfiguracao("samlink_dbname");
            $samlinkUser 	= pegaConfiguracao("samlink_user");

            $dddRes = substr($fone,0,2);
            $foneRes = substr($fone,2,8);
            $dddCom = substr($fax,0,2);
            $foneCom = substr($fax,2,8);
            $cep1 = substr($cep,0,5);
            $cep2 = substr($cep,5,3);

            //Carrega o cnpj em partes para preencher os campos segmentados
            if (isset($cnpj)) {
                if ($cnpj) {
                    $cnpj1 = substr($cnpj,0,2);
                    $cnpj2 = substr($cnpj,2,3);
                    $cnpj3 = substr($cnpj,5,3);
                    $cnpj4 = substr($cnpj,8,4);
                    $cnpj5 = substr($cnpj,12,2);
                    $cnpj = $cnpj1.".".$cnpj2.".".$cnpj3."/".$cnpj4."-".$cnpj5;
                }
            }

            include CAM_FW_LEGADO."configuracaoLegado.class.php";
            $configuracao = new configuracaoLegado;
            $configuracao->setaEstadoAtual($codUf);
            $configuracao->listaComboEstados();
        ?>
        <script type="text/javascript">

            function VerificaDiretorio(diretorio)
            {
               window.parent.frames["oculto"].document.location = "configuracaoBasica.php?<?=Sessao::getId();?>&ctrl=3&diretorio="+diretorio;
            }

            function Valida()
            {
                var mensagem = "";
                var erro = false;
                var campo;
                var campoaux;
                        var nomeCampo;
                var tamanho;
                        var ultimaposicao;

                campo = document.frm.nom_prefeitura.value;
                if (campo == "") {
                    mensagem += "@Campo Nome Prefeitura  inválido! ()";
                    erro = true;
                }

                campo = document.frm.cod_uf.value;
                if ((campo == "") || (campo == "Selecione")) {
                    mensagem += "@Campo Estado inválido! ()";
                    erro = true;
                }

                campo = document.frm.cod_municipio.value;
                if ((campo == "") || (campo == "Selecione")) {
                    mensagem += "@Campo Municipio inválido! ()";
                    erro = true;
                }

                campo = document.frm.logotipo.value;
                if (campo == "") {
                    mensagem += "@Campo logotipo inválido! ()";
                    erro = true;
                }

                campo = document.frm.periodo_auditoria.value;
                if (campo == "") {
                    mensagem += "@Campo Período Auditoria inválido! ()";
                    erro = true;
                }

                campo = document.frm.periodo_auditoria.value;
                if (isNaN(campo)) {
                    mensagem += "@Campo Período Auditoria inválido! ()";
                    erro = true;
                }

                campo = document.frm.diretorio.value;
                tamanho = campo.length;
                if ((campo == "")||(campo==" ")) {
                    mensagem += "@Campo Caminho da Raiz do Sistema inválido! ()";
                    erro = true;
                } else {
                    if (campo.substring(tamanho-1,tamanho) == "/") {
                        document.frm.diretorio.value =campo.substring(0,tamanho-1);
                    }
                }

                campo = document.frm.anoExercicio.value;
                if (campo == "") {
                    mensagem += "@Campo Exercício inválido! ()";
                    erro = true;
                }
                campo = document.frm.mascaraSetor.value;
                if (campo == "") {
                    mensagem += "@Campo Máscara para Setor inválido! ()";
                    erro = true;
                }
                campo = document.frm.mascaraLocal.value;
                if (campo == "") {
                    mensagem += "@Campo Máscara para Local inválido! ()";
                    erro = true;
                }

                campo = document.frm.cnpj.value.length;
                if (campo<18) { // Campo cnpj tem que ter 14 caracteres >
                    mensagem += "@Campo CNPJ inválido!("+document.frm.cnpj.value+")";
                    erro = true;
                }

                campoaux = document.frm.cnpj.value;
                var expReg = new RegExp("[^a-zA-Z0-9]","g");
                var campoAuxDesmasc = campoaux.replace(expReg, '');
                if (campo==18) {
                    if (!VerificaCNPJ(campoAuxDesmasc)) { //> Verifica se o CNPJ é válido
                        mensagem += "@Campo CNPJ inválido!("+campoaux+")";
                        erro = true;
                    }
                }

                campo = document.frm.populacao.value;
                if ( campo != '' && !isInt( campo ) ) {
                    mensagem += "@Campo População inválido!";
                    erro = true;
                }

                campo = document.frm.cep1.value.length;
                campoaux = document.frm.cep2.value.length;
                if (campo==0 && campoaux==0) {
                    mensagem += "@Campo CEP inválido ()";
                    erro = true;
                }

                campo = document.frm.inCodIGBE.value;
                if ( campo == '' ) {
                    mensagem += "@Campo Código do Município do IBGE inválido!";
                    erro = true;
                }

                campo = document.frm.email.value.length;
                if (campo>0) {
                    if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.frm.email.value))) {
                       erro = true;
                    }
                }

        <?php
        //SAMLINK
        if ($samlinkExiste) {
        ?>
                campo = document.frm.samlink_host.value;
                if (campo == "") {
                    mensagem += "@Campo Host inválido! ()";
                    erro = true;
                }

                campo = document.frm.samlink_port.value;
                if (campo == "") {
                    mensagem += "@Campo Porta inválido! ()";
                    erro = true;
                }

                campo = document.frm.samlink_dbname.value;
                if (campo == "") {
                    mensagem += "@Campo Base de Dados inválido! ()";
                    erro = true;
                }

                campo = document.frm.samlink_user.value;
                if (campo == "") {
                    mensagem += "@Campo Usuário inválido! ()";
                    erro = true;
                    }
                    <?php
                    }
                    ?>

                if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
                    return !(erro);

            }// Fim da function Valida

            //A função salvar testa a validação, e se tudo ocorrer certo, envia o form
            function Salvar()
            {
                if (Valida()) {
                    document.frm.submit();
                }
            }

            function atualizaMunicipio(uf)
            {
                window.parent.frames["oculto"].document.location = "configuracaoBasica.php?<?=Sessao::getId();?>&ctrl=1&cod_uf="+uf;
            }

        </script>

        <form name="frm" action="configuracaoBasica.php?<?=Sessao::getId();?>" method="POST">
          <table width="100%" cellspacing=2 border=0 cellpadding=2>
              <tr><td class=alt_dados colspan=2>Dados da Entidade Principal</td></tr>
                <tr>
                    <td class=label width="30%">*Nome da Entidade Principal</td>
                    <td class=field width="70%">
                        <input type="text" name="nom_prefeitura" size=60 maxlength=60 value="<?=$nomPrefeitura;?>"></td>
                </tr>
                <tr>
                    <td class=label>*Estado</td><td class=field>
    <?php
        $vcodUf = $codUf;
        $sSQL = "SELECT cod_uf,nom_uf FROM sw_uf Where cod_uf > 0 and cod_pais = 1 ORDER BY nom_uf";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $comboUf = "";
        $comboUf .= "<select name=cod_uf onchange='javascript: atualizaMunicipio(this.value);'  style='width: 300px'>\n";
        while (!$dbEmp->eof()) {
            $nomUf = trim($dbEmp->pegaCampo("nom_uf"));
            $codUf = $dbEmp->pegaCampo("cod_uf");
            $dbEmp->vaiProximo();
            $comboUf .= "<option value=".$codUf;
            if ($vcodUf == $codUf)
                $comboUf .= " SELECTED";
            $comboUf .=">".$nomUf."</option>\n";
        }
        $comboUf .= "</select>";

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $comboUf;
    ?>
    </td>
    </tr>

    <tr>
    <td class=label>*Município</td><td class=field>

    <?php
        $vnomMunicipio = $nomMunicipio;
        $sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$vcodUf." ORDER BY nom_municipio";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $comboMunicipio = "";
        $comboMunicipio .= "<select name='cod_municipio' style='width: 300px'>\n";
        $comboMunicipio .= "<option value='Selecione'>Selecione</option>";
        while (!$dbEmp->eof()) {
            $nomMunicipio = trim($dbEmp->pegaCampo("nom_municipio"));
            $codMunicipio = trim($dbEmp->pegaCampo("cod_municipio"));
            $dbEmp->vaiProximo();
            $comboMunicipio .= "<option value='".$codMunicipio."'";
            if (trim($cod_municipio) == trim($codMunicipio))
                $comboMunicipio .= " SELECTED";
                $comboMunicipio .=">".$nomMunicipio."</option>\n";
        }
        $comboMunicipio .= "</select>";

        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
        echo $comboMunicipio;
    ?>

    </td>
    </tr>

    <td class=label>*Código do Município do IBGE</td>
    <td class=field>
    <?php
        if ( strstr($codMunicipioIBGE,'cod_municipio_ibge') == true ) {
            $codMunicipioIBGE = '';
        }
        require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
        $obTextMunicipioIBGE = new TextBox();
        $obTextMunicipioIBGE->setId        ( 'inCodIGBE' );
        $obTextMunicipioIBGE->setName      ( 'inCodIGBE' );
        $obTextMunicipioIBGE->setSize      ( 5 );
        $obTextMunicipioIBGE->setMaxLength ( 7 );
        $obTextMunicipioIBGE->setInteiro   ( true  );
        $obTextMunicipioIBGE->setNull      ( false );
        $obTextMunicipioIBGE->setValue     ( $codMunicipioIBGE );
        $obTextMunicipioIBGE->montaHTML();
        echo $obTextMunicipioIBGE->getHTML();
    ?>
    </td>
    </tr>

    <tr>
    <td class=label>Tipo Logradouro</td><td class=field>

    <?php
            $sSQL = "SELECT nom_tipo FROM sw_tipo_logradouro ORDER by nom_tipo";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $comboLogradouro = "";
            $comboLogradouro .= "<select name=tipo_logradouro>\n";
            while (!$dbEmp->eof()) {
                $nomLogradouro = trim($dbEmp->pegaCampo("nom_tipo"));
                $dbEmp->vaiProximo();
                $comboLogradouro .= "<option value=".$nomLogradouro;
                if ($nomLogradouro == $tipoLogradouro)
                    $comboLogradouro .= " SELECTED";
                $comboLogradouro .=">".$nomLogradouro."</option>\n";
            }
            $comboLogradouro .= "</select>";
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
            echo $comboLogradouro;
    ?>

    </td>
    </tr>

    <tr>
    <td class=label>Logradouro</td>
    <td class=field><input type="text" name="logradouro" size=60 maxlength=60 value="<?=$logradouro;?>"></td>
    </tr>

    <tr>
    <td class=label>Número</td><td class=field><input type="text" name="numero" size=5 maxlength=6 value="<?=$numero;?>"></td>
    </tr>

    <tr>
    <td class=label>Complemento</td><td class=field><input type="text" name="complemento" size=20 maxlength=20 value="<?=$complemento;?>"></td>
    </tr>

    <tr>
    <td class=label>Bairro</td><td class=field><input type="text" name="bairro" size=30 maxlength=30 value="<?=$bairro;?>"></td>
    </tr>

    <td class="label">*CEP</td>
    <td class="field">
    <input type="text" name="cep1" maxlength="5" size="5" onKeyUp="return autoTab(this, 5, event);" value="<?=$cep1;?>">&nbsp;<b>-</b>
    <input type="text" name="cep2" maxlength="3" size="3" onKeyUp="return autoTab(this, 3, event);" value="<?=$cep2;?>">
    </td>
    </tr>
    <tr>
    <td class="label">Telefone</td>
    <td class="field">
    <input type="text" name="dddRes" size=2 maxlength=2 value="<?=$dddRes;?>" onKeyUp="return autoTab(this, 2, event);">&nbsp;<b>-</b>
    <input type="text" name="foneRes" maxlength="8" size="8" value="<?=$foneRes;?>" onKeyUp="return autoTab(this, 8, event);">
    </td>
    </tr>
    <tr>
    <td class="label">Fax</td>
    <td class="field">
    <input type="text" name="dddCom" size=2 maxlength=2 value="<?=$dddCom;?>" onKeyUp="return autoTab(this, 2, event);">&nbsp;<b>-</b>
    <input type="text" name="foneCom" maxlength="8" size="8" value="<?=$foneCom;?>"onKeyUp="return autoTab(this, 8, event);">
    </td>
    </tr>
    <tr>

    <tr>
        <td class=label>E-mail</td>
        <td class=field><input type="text" name="email" size=30 maxlength=40 value="<?=$email;?>"></td>
    </tr>

    <tr>
        <td class=label>Site</td>
        <td class=field><input type="text" name="site" size=30 maxlength=40 value="<?=$site;?>"></td>
    </tr>

    <tr>
        <td class="label">*CNPJ</td>
        <td class="field">
            <input type="text" name="cnpj" maxlength="18" size="20" value="<?=$cnpj;?>"
            onKeyUp="JavaScript: mascaraCNPJ(this, event);"
            onKeyPress="return(isValido(this, event, '0123456789'));">
        </td>
    </tr>

    <tr>
    <td class=label>População</td><td class=field><input type="text" name="populacao" size=10 maxlength=20 value="<?=$populacao;?>"></td>
    </tr>

    <tr>
    <td class=label>Prefeito</td>
    <td class=field>
    <?php
        require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
        $obIPopUpCGM = new IPopUpCGM( new Form);
        $obIPopUpCGM->setTipo ( "fisica" );
        $obIPopUpCGM->setObrigatorioBarra( true );
        $obIPopUpCGM->setNull            ( true );
        $obIPopUpCGM->obCampoCod->setValue( $prefeito );
        $obIPopUpCGM->obCampoCod->setId   ( $obIPopUpCGM->obCampoCod->getName() );
        if ( $prefeito )
           $obIPopUpCGM->setValue ( SistemaLegado::pegaDado('nom_cgm','sw_cgm','where numcgm='.$prefeito) );
        $obIPopUpCGM->montaHTML();
        echo $obIPopUpCGM->getHTML();
    ?>
    </td>
    </tr>

    <tr>
    <td class=label>Diário Oficial</td>
    <td class=field>
    <?php
        require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
        $obIPopUpCGMD = new IPopUpCGM( new Form);
        $obIPopUpCGMD->setTipo             ( "juridica" );
        $obIPopUpCGMD->setObrigatorioBarra ( true       );
        $obIPopUpCGMD->setNull             ( true       );
        $obIPopUpCGMD->setId               ( 'stNomCGMD');
        $obIPopUpCGMD->setName             ( 'stNomCGMD');
        $obIPopUpCGMD->obCampoCod->setValue( $diario    );
        $obIPopUpCGMD->obCampoCod->setId   ( 'inCGMD'   );
        $obIPopUpCGMD->obCampoCod->setName ( 'inCGMD'   );
        if ( $diario )
           $obIPopUpCGMD->setValue ( SistemaLegado::pegaDado('nom_cgm','sw_cgm','where numcgm='.$diario) );
        $obIPopUpCGMD->montaHTML();
        echo $obIPopUpCGMD->getHTML();
    ?>
    </td>
    </tr>

      <tr><td class=alt_dados colspan=2>Parâmetros para o sistema</td></tr>
    <tr>
            <td class=label>*Logotipo</td>
            <td class=field><input type="text" readonly=true name="logotipo" size=30 value="<?=$logotipo;?>" onKeyPress="return isValido(this.value)">&nbsp;
                            <a href="javascript:MostraImageUpload('<?=Sessao::getId();?>');"><img src="<?=CAM_FW_IMAGENS."procuracgm.gif";?>" alt="" border=0></a></td>
    </tr>

    <tr>
    <td class=label>*Período de Auditoria</td><td class=field>
    <input type="text" name="periodo_auditoria" size=4 maxlength=4 value="<?=$periodoAuditoria;?>"></td>
    </tr>

    <tr>
    <td class=label>Mostrar Nome do Usuário no Relatório</td><td class=field>

    <select name="relatorio_usuario">
    <option value="S"
    <?php
    if ($usuarioRelatorio == 'S')
    echo " SELECTED";
    ?>
    >Sim</option>
    <option value="N"
    <?php
    if ($usuarioRelatorio == 'N')
    echo " SELECTED";
    ?>
    >Não</option>
    </select>

    </td>
    </tr>

    <tr>
    <td class=label>*Caminho da Raiz do Sistema</td>
    <td class=field>
        <input type="text" name="diretorio" size=60 maxlength=60 value="<?=$diretorio;?>"
        onBlur="return VerificaDiretorio(this.value)"
            >
    </td>
    </tr>

    <tr>
        <td class=label>*Exercício</td>
        <td class=field>
            <input type="text" name="anoExercicio" size=4 maxlength=4 value="<?=$anoExercicio;?>">
        </td>
    </tr>
    <tr>
        <td class=label>*Máscara para setor</td>
        <td class=field>
            <input type="text" name="mascaraSetor" size=60 maxlength=60 value="<?=$mascaraSetor;?>">
        </td>
    </tr>
    <tr>
        <td class=label>*Máscara para local</td>
        <td class=field>
            <input type="text" name="mascaraLocal" size=60 maxlength=60 value="<?=$mascaraLocal;?>">
        </td>
    </tr>
    <?php
    //SAMLINK

    if ($samlinkExiste) {

    ?>
    </tr>
      <tr><td class=alt_dados colspan=2>Parâmetros para Samlink</td></tr>
    <tr>
            <td class=label>*Host</td>
            <td class=field><input type="text" name="samlink_host" size=30 value="<?=$samlinkHost?>">&nbsp;</td>
    </tr>
    <tr>
            <td class=label>*Porta</td>
            <td class=field><input type="text" name="samlink_port" size=30 value="<?=$samlinkPort?>">&nbsp;</td>
    </tr>
    <tr>
            <td class=label>*Banco de Dados</td>
            <td class=field><input type="text" name="samlink_dbname" size=30 value="<?=$samlinkDBName?>">&nbsp;</td>
    </tr>
    <tr>
            <td class=label>*Usuário</td>
            <td class=field><input type="text" name="samlink_user" size=30 value="<?=$samlinkUser?>">&nbsp;</td>
    </tr>

    <?php
    //FECHA SAMLINK
    }
    ?>

    <tr>
    <td class=field colspan=2>
    <?php geraBotaoOk(); ?>
    </td>
    </tr>

    </table>
    </form>
    <?php

} else {

    $nom_prefeitura       = $request->get('nom_prefeitura');
    $cod_municipio        = $request->get('cod_municipio');
    $cod_uf               = $request->get('cod_uf');
    $nom_municipio        = $request->get('nom_municipio');
    $tipo_logradouro      = $request->get('tipo_logradouro');
    $logradouro           = $request->get('logradouro');
    $numero               = $request->get('numero');
    $complemento          = $request->get('complemento');
    $bairro               = $request->get('bairro');
    $cep1                 = $request->get('cep1');
    $cep2                 = $request->get('cep2');
    $dddRes               = $request->get('dddRes');
    $foneRes              = $request->get('foneRes');
    $dddCom               = $request->get('dddCom');
    $foneCom              = $request->get('foneCom');
    $email                = $request->get('email');
    $site                 = $request->get('site');
    $populacao            = $request->get('populacao');
    $cnpj                 = $request->get('cnpj');
    $logotipo             = $request->get('logotipo');
    $periodo_auditoria    = $request->get('periodo_auditoria');
    $relatorio_usuario    = $request->get('relatorio_usuario');
    $diretorio            = $request->get('diretorio');
    $anoExercicio         = $request->get('anoExercicio');
    $mascaraSetor         = $request->get('mascaraSetor');
    $mascaraLocal         = $request->get('mascaraLocal');    
    $samlink_host         = $request->get('samlink_host');
    $samlink_port         = $request->get('samlink_port');
    $samlink_dbname       = $request->get('samlink_dbname');
    $samlink_user         = $request->get('samlink_user');
    $codMunicipioIBGE     = $request->get('inCodIGBE');

    include_once( CAM_GA_ADM_NEGOCIO."RAdministracaoConfiguracao.class.php" );

    $obRAdministracaoConfiguracao = new RAdministracaoConfiguracao;

    $fone = $dddRes.$foneRes;
    $fax = $dddCom.$foneCom;
    $cep = $cep1.$cep2;
    $cnpj = preg_replace('/[^a-zA-Z0-9]/','', $cnpj );
    
    $obRAdministracaoConfiguracao->setCodModulo( 2 );
    $obRAdministracaoConfiguracao->setExercicio( Sessao::getExercicio() );
    $obRAdministracaoConfiguracao->addConfiguracao( 'nom_prefeitura'            , $nom_prefeitura       );
    $obRAdministracaoConfiguracao->addConfiguracao( 'cod_municipio'             , $cod_municipio        );
    $obRAdministracaoConfiguracao->addConfiguracao( 'cod_uf'                    , $cod_uf               );
    $obRAdministracaoConfiguracao->addConfiguracao( 'nom_municipio'             , $nom_municipio        );
    $obRAdministracaoConfiguracao->addConfiguracao( 'tipo_logradouro'           , $tipo_logradouro      );
    $obRAdministracaoConfiguracao->addConfiguracao( 'logradouro'                , $logradouro           );
    $obRAdministracaoConfiguracao->addConfiguracao( 'numero'                    , $numero               );
    $obRAdministracaoConfiguracao->addConfiguracao( 'complemento'               , $complemento          );
    $obRAdministracaoConfiguracao->addConfiguracao( 'bairro'                    , $bairro               );
    $obRAdministracaoConfiguracao->addConfiguracao( 'cep'                       , $cep                  );
    $obRAdministracaoConfiguracao->addConfiguracao( 'fone'                      , $fone                 );
    $obRAdministracaoConfiguracao->addConfiguracao( 'fax'                       , $fax                  );
    $obRAdministracaoConfiguracao->addConfiguracao( 'e_mail'                    , $email                );
    $obRAdministracaoConfiguracao->addConfiguracao( 'site'                      , $site                 );
    $obRAdministracaoConfiguracao->addConfiguracao( 'cnpj'                      , $cnpj                 );
    $obRAdministracaoConfiguracao->addConfiguracao( 'populacao'                 , $populacao            );
    $obRAdministracaoConfiguracao->addConfiguracao( 'CGMPrefeito'               , $_REQUEST['inCGM']    );
    $obRAdministracaoConfiguracao->addConfiguracao( 'CGMDiarioOficial'          , $_REQUEST['inCGMD']   );
    $obRAdministracaoConfiguracao->addConfiguracao( 'logotipo'                  , $logotipo             );
    $obRAdministracaoConfiguracao->addConfiguracao( 'periodo_auditoria'         , $periodo_auditoria    );
    $obRAdministracaoConfiguracao->addConfiguracao( 'usuario_relatorio'         , $relatorio_usuario    );
    $obRAdministracaoConfiguracao->addConfiguracao( 'diretorio'                 , $diretorio            );
    $obRAdministracaoConfiguracao->addConfiguracao( 'ano_exercicio'             , $anoExercicio         );
    $obRAdministracaoConfiguracao->addConfiguracao( 'mascara_setor'             , $mascaraSetor         );
    $obRAdministracaoConfiguracao->addConfiguracao( 'mascara_local'             , $mascaraLocal         );    
    $obRAdministracaoConfiguracao->addConfiguracao( 'cod_municipio_ibge'        , $codMunicipioIBGE );
    if ($samlinkExiste) {
        $obRAdministracaoConfiguracao->addConfiguracao( 'samlink_host',$samlink_host     );
        $obRAdministracaoConfiguracao->addConfiguracao( 'samlink_port',$samlink_port     );
        $obRAdministracaoConfiguracao->addConfiguracao( 'samlink_dbname',$samlink_dbname );
        $obRAdministracaoConfiguracao->addConfiguracao( 'samlink_user',$samlink_user     );
    }
    $obErro = $obRAdministracaoConfiguracao->alterarConfiguracao();
    if ( !$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso('configuracaoBasica.php','Configuração',"alterar","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    }

}
break;

case 1:
    if ($_REQUEST["cod_uf"]) {
        $cod_uf=$_REQUEST["cod_uf"];
    }

    if (isset($cod_uf) and $cod_uf > 0) {
        $js = "";
        $sSQL = "SELECT * FROM sw_municipio
                 WHERE cod_uf = ".$cod_uf."
                 ORDER by nom_municipio";
                $dbEmp = new dataBaseLegado;
                $dbEmp->abreBD();
                $dbEmp->abreSelecao($sSQL);
                $dbEmp->vaiPrimeiro();
                $comboMunicipio = "";
                $js .= "limpaSelect(f.cod_municipio,0); \n";
                $js .= "f.cod_municipio.options[0] = new Option('Selecione','','selected'); \n";
                $iCont = 1; //zero eh o selecione!
                while (!$dbEmp->eof()) {
                    $codg_municipio  = trim($dbEmp->pegaCampo("cod_municipio"));
                    $nomg_municipio  = trim($dbEmp->pegaCampo("nom_municipio"));
                    $dbEmp->vaiProximo();
                    $js .= "f.cod_municipio.options[".$iCont++."] = new Option('".$nomg_municipio."','".$codg_municipio."');\n";
                }
                $dbEmp->limpaSelecao();
                $dbEmp->fechaBD();
                SistemaLegado::executaFrameOculto($js);
    }
break;

case 3:

    $diretorio = $_REQUEST['diretorio'];

    $js = "";
    if (($diretorio!=' ') and (!is_dir($diretorio))) {
        $js .= "if (f.diretorio.value != '') {\n";
        $js .= "    alertaAviso('Campo Caminho da Raiz do Sistema inválido!','form','erro','Sessao::getId()');\n";
        $js .= "    f.diretorio.value = '';\n";
        $js .= "    f.diretorio.focus();\n";
        $js .= "}\n";
    }
        executaFrameOculto($js);
break;

}
?>
