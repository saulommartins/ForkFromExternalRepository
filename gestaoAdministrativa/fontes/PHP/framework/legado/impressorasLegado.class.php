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
    *
    * Data de Criação: 27/10/2005

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Cassiano de Vasconcellos Ferreira

    * @package framework
    * @subpackage componentes

    * Casos de uso: uc-01.01.00

    $Id: impressorasLegado.class.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    */

class impressorasLegado
{
    /*** Declaração ***/
    public $codImpressora;
    public $nomImpressora;
    public $localizacao;
    public $filaImpressao;
    public $codOrgao;
    public $codUnidade;
    public $codDepartamento;
    public $codSetor;
    public $codLocal;
    public $anoExercicioLocal;
    public $stErro;

    # Método construtor
    public function impressorasLegado()
    {
        $this->codImpressora = "";
        $this->nomImpressora = "";
        $this->filaImpressao = "";
        $this->codOrgao = "";
        $this->codLocal = "";
        #$this->localizacao = "";
        #$this->codUnidade = "";
        #$this->codDepartamento = "";
        #$this->codSetor = "";
        #$this->anoExercicioLocal = "";
    }

    # Insere Impressoras
    public function insereImpresssora()
    {
        $anoE = pegaConfiguracao("ano_exercicio");
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "INSERT INTO
                   administracao.impressora
                   (
                        cod_impressora,
                        nom_impressora,
                        cod_orgao,
                        cod_local,
                        fila_impressao
                    )
                    VALUES
                    (
                        '".$this->codImpressora."',
                        '".$this->nomImpressora."',
                         ".$this->codOrgao.",
                         ".$this->codLocal.",
                        '".$this->filaImpressao."'
                    )";

        # echo $insert."<br>\n";
        # print ($dbConfig->executaSql($insert));
        # die('fim insert');

        return ($dbConfig->executaSql($insert) ? true : false);
        $dbConfig->fechaBd();
    }

    # Pega variáves para Inserção
    public function setaVariaveis($cod_impressora, $nom_impressora, $fila_impressao, $codOrgao, $codLocal)
    {
        $this->codImpressora     = $cod_impressora;
        $this->nomImpressora     = $nom_impressora;
        $this->filaImpressao     = $fila_impressao;
        $this->codOrgao		     = $codOrgao;
        $this->codLocal          = $codLocal;
    }

    # Pega variáves para Ediçao
    public function setaVariaveisUpdate($cod_impressora, $nom_impressora, $localizacao, $fila_impressao)
    {
        $this->codImpressora = $cod_impressora;
        $this->nomImpressora = $nom_impressora;
        $this->localizacao   = $localizacao;
        $this->filaImpressao = $fila_impressao;
    }

    # Pega variáves apenas Código
    public function pegaCodImpressora($cod_impressora)
    {
        $this->codImpressora = $cod_impressora;
    }

    # Lista Impreessoras para edição
    public function listaImpresora()
    {
        $sSQL = " SELECT
                        imp.cod_impressora,
                        imp.nom_impressora,
                        nom_orgao,
                        nom_unidade,
                        nom_departamento,
                        nom_setor,
                        nom_local,
                        imp.exercicio,
                        imp.fila_impressao,
                        imp.cod_orgao,
                        imp.cod_unidade,
                        imp.cod_departamento,
                        imp.cod_setor,
                        imp.cod_local
                    FROM
                        administracao.impressora as imp,
                        administracao.orgao as orgao,
                        administracao.unidade as unidade,
                        administracao.departamento as departamento,
                        administracao.setor as setor,
                        administracao.local as local
                    WHERE
                        imp.cod_orgao = orgao.cod_orgao and
                        imp.cod_unidade = unidade.cod_unidade and
                        imp.cod_departamento = departamento.cod_departamento and
                        imp.cod_setor = setor.cod_setor and
                        imp.cod_local = local.cod_local and
                        local.cod_setor = setor.cod_setor and
                        local.cod_departamento = departamento.cod_departamento and
                        local.cod_unidade = unidade.cod_unidade and
                        local.cod_orgao = orgao.cod_orgao and
                        setor.cod_departamento = departamento.cod_departamento and
                        setor.cod_unidade = unidade.cod_unidade and
                        setor.cod_orgao = orgao.cod_orgao and
                        departamento.cod_unidade = unidade.cod_unidade and
                        departamento.cod_orgao = orgao.cod_orgao and
                        unidade.cod_orgao = orgao.cod_orgao and
                        cod_impressora > 0";
        //echo $sSQL."<br>\n";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $lista="";
        echo "<tr><td class=alt_dados>Órgão</td><td class=alt_dados>Unidade</td><td class=alt_dados>Departamento</td><td class=alt_dados>Setor</td><td class=alt_dados>Local</td><td class=alt_dados>Código</td><td class=alt_dados>Nome da Impressora</td><td class=alt_dados>Fila de Impressão</td><td class=alt_dados>Editar</td></tr>";
        while (!$dbEmp->eof()) {
            $nom_impressora  = trim($dbEmp->pegaCampo("nom_impressora"));
            $cod_impressora  = trim($dbEmp->pegaCampo("cod_impressora"));
            $anoE  = trim($dbEmp->pegaCampo("exercicio"));
            $codLocalizacao  = trim($dbEmp->pegaCampo("cod_orgao").".".$dbEmp->pegaCampo("cod_unidade").".".$dbEmp->pegaCampo("cod_departamento").".".$dbEmp->pegaCampo("cod_setor").".".$dbEmp->pegaCampo("cod_local"));
            $localizacao = $dbEmp->pegaCampo("nom_orgao").".".$dbEmp->pegaCampo("nom_unidade").".".$dbEmp->pegaCampo("nom_departamento").".".$dbEmp->pegaCampo("nom_setor").".".$dbEmp->pegaCampo("nom_local");
            $filaImpressao = $dbEmp->pegaCampo("fila_impressao");
            $organograma = explode(".", $localizacao);
            $dbEmp->vaiProximo();
            $lista .= "<tr><td class=show_dados>".$organograma[0]."</td><td class=show_dados>".$organograma[1]."</td><td class=show_dados>".$organograma[2]."</td><td class=show_dados>".$organograma[3]."</td><td class=show_dados>".$organograma[4]."</td><td class=show_dados>".$cod_impressora."</td><td class=show_dados>".$nom_impressora."</td><td class=show_dados>".$filaImpressao."</td><td class=show_dados>
            <a href='' onClick=\"alertaQuestao('../administracao/impressoras/excluiImpressora.php','cod_impressora','".$cod_impressora."','".$nom_impressora."','sn_excluir','".Sessao::getId()."')\";>
            <img src=".CAM_FW_IMAGENS."btnexcluir.gif border=0></a></td></tr>";
        }

        echo $lista;
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();

    }

    # Lista Impreessoras para Apagar
    public function listaImpresoraApagar()
    {
        $sSQL = "select * from administracao.impressora where cod_impressora > 0 order by nom_impressora";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $lista="";
        echo "<tr><td class=alt_dados colspan=3>Selecione a Impressora que deseja Apagar</td></tr>";
        while (!$dbEmp->eof()) {
                $nom_impressora  = trim($dbEmp->pegaCampo("nom_impressora"));
            $cod_impressora  = trim($dbEmp->pegaCampo("cod_impressora"));
                $dbEmp->vaiProximo();
                $lista .= "<tr><td class=show_dados><b>".$nom_impressora."</b></td><td class=show_dados width=78>
            <a href='' onClick=\"alertaQuestao('../administracao/impressoras/excluiImpressora.php','cod_impressora','".$cod_impressora."','".$nom_impressora."','sn_excluir','".Sessao::getId()."')\";>
            <img src=".CAM_FW_IMAGENS."btnexcluir.gif border=0></a></td></tr>";
        }
        echo $lista;
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
    }

    # Lista FORM impressora para editar
    public function editaImpressoras()
    {
        $sSQL = "select * from administracao.impressora WHERE cod_impressora = '$this->codImpressora'";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $lista="";
    ?>
    <script type="text/javascript">

      function Valida()
      {
        var mensagem = "";
        var erro = false;
        var campo;
        var campoaux;

        campo = document.frm.nom_impressora.value.length;
            if (campo == 0) {
            mensagem += "@O campo Nome da Impressora é obrigatório";
            erro = true;
         }

         campo = document.frm.localizacao.value.length;
            if (campo == 0) {
            mensagem += "@O campo Localização é Obrigatório";
            erro = true;
         }

         campo = document.frm.localizacao.value.length;
            if (campo == 0) {
            mensagem += "@O campo Fila de Impressão é Obrigatório";
            erro = true;
         }

            if (erro) alertaAviso(mensagem,'form','erro');
            return !(erro);
      }
      function Salvar()
      {
         if (Valida()) {
            document.frm.action="alteraImpressoraForm.php?<?=Sessao::getId();?>&ctrl=1";
            document.frm.submit();
         }
      }
   </script>
    <?php
    while (!$dbEmp->eof()) {
            $nom_impressora  = trim($dbEmp->pegaCampo("nom_impressora"));
            $cod_impressora  = trim($dbEmp->pegaCampo("cod_impressora"));
            $localizacao  = trim($dbEmp->pegaCampo("localizacao"));
            $fila_impressao  = trim($dbEmp->pegaCampo("fila_impressao"));
            $dbEmp->vaiProximo();
            $lista .= '
                        <form name=frm method="post" action="alteraImpressoraForm.php">
                           <table width=400>
                           <tr><td class=alt_dados colspan=2>Altere os dados da Impressoras</td></tr>
                           <tr><td class=label>*Nome da Impressora:</td><td class=field>
                           <input type="text" name="nom_impressora" value="'.$nom_impressora.'" size=15 maxlength=30></td></tr>
                           <tr><td class=label>*Localização</td><td class=field>
                           <input type="text" name="localizacao" value="'.$localizacao.'" size=30 maxlength=60></td></tr>
                           <tr><td class=label>*Fila de Impressão</td><td class=field>
                           <input type="text" name="fila_impressao" value="'.$fila_impressao.'" size=10 maxlength=15></td></tr>
                           <tr><input type="hidden" name="cod_impressora" value="'.$cod_impressora.'">
                           <td class=field colspan=3>
                           <input type="button" name="ok" Value="OK" onclick="Salvar();" style="width:60px" class="botao">
                           <input type="reset" name="Limpar" value="Limpar" class="botao">
                           <div align="right"><b>* Campos Obrigatórios</b></div></td></tr></table>
                           </form>';
    }
        echo $lista;
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
        }

    # Edita Impressoras
    public function updateImpresssora()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();

        $insert = "	UPDATE  administracao.impressora
                       SET  nom_impressora = '".$this->nomImpressora."',
                            fila_impressao = '".$this->filaImpressao."',
                            cod_orgao = ".$this->codOrgao.",
                            cod_local = ".$this->codLocal."
                       WHERE  cod_impressora = ".$this->codImpressora;

         # echo $insert."<br>\n";
         # die();

        if ($dbConfig->executaSql($insert)) {
            $dbConfig->fechaBd();

            return true;
        } else {
            $dbConfig->fechaBd();

            return false;
        }
    }

    # Apaga Impressoras
    public function deleteImpresssora()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();

        $delete = "DELETE
                     FROM administracao.impressora
                    WHERE cod_impressora = ".$this->codImpressora;

        if ($dbConfig->executaSql($delete)) {
            $boRetorno = true;
        } else {
            $boRetorno = false;
            $this->stErro = $dbConfig->pegaUltimoErro();
        }
        $dbConfig->fechaBd();

        return $boRetorno;
    }
}
