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
* Classe de negócio Andamento
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 4113 $
$Name$
$Author: lizandro $
$Date: 2005-12-20 15:27:43 -0200 (Ter, 20 Dez 2005) $

Casos de uso: uc-01.06.98
*/

class andamento
{
/**************************************************************************/
/**** Declaração das variáveis                                          ***/
/**************************************************************************/
    public $comboTipoProcesso;
    public $assunto;
    public $classificacao;
    public $codOrgao;
    public $codUnidade;
    public $codDepartamento;
    public $codSetor;
    public $numPassagens;
    public $anoE;
    public $descricao;
    public $ordem;
    public $numDia;
    public $valorAlteracao;
    public $codProcesso;
    public $codAndamento;

/**************************************************************************/
/**** Método Construtor                                                 ***/
/**************************************************************************/
    public function andamento()
    {
        $this->comboTipoProcesso = "";
        $this->assunto = "";
        $this->classificacao = "";
        $this->codOrgao = "";
        $this->codUnidade = "";
        $this->codDepartamento = "";
        $this->codSetor = "";
        $this->numPassagens = "";
        $this->anoE = "";
        $this->descricao = "";
        $this->ordem = "";
        $this->numDia = "";
        $this->valorAlteracao = "";
        $this->codProcesso = "";
        $this->codAndamento = "";

        }

/**************************************************************************/
/**** Mostra o na tela o Combo por módulos gerado                       ***/
/**************************************************************************/
    public function mostraComboTipoProcessos()
    {
        echo $this->comboTipoProcesso;
    }

/**************************************************************************/
/**** Pega as variáveis de ação do usuário                              ***/
/**************************************************************************/
    public function setaAndamentoPadrao($assunto, $classificacao, $codOrgao, $codUnidade, $codDepartamento, $codSetor, $numPassagens, $anoE, $descricao, $ordem, $numDia)
    {
        $this->assunto = $assunto;
        $this->classificacao = $classificacao;
        $this->codOrgao = $codOrgao;
        $this->codUnidade = $codUnidade;
        $this->codDepartamento = $codDepartamento;
        $this->codSetor = $codSetor;
        $this->numPassagens = $numPassagens;
        $this->anoE = $anoE;
        $this->descricao = $descricao;
        $this->ordem = $ordem;
        $this->numDia = $numDia;
        }
/**************************************************************************/
/**** Insere na tabela Anamento Padrão                                  ***/
/**************************************************************************/
    public function insereAndamentoPadrao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "INSERT INTO sw_andamento_padrao (cod_assunto, cod_classificacao, cod_orgao, num_passagens, descricao, ordem, num_dia) VALUES ('".$this->assunto."', '".$this->classificacao."', '".$this->codOrgao."', '".$this->numPassagens."', '".$this->descricao."', '".$this->ordem."', '".$this->numDia."')";

        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

/**************************************************************************/
/**** Faz o update dos dados para andamento padrão                      ***/
/**************************************************************************/
    public function updateAndamentoPadrao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $sSQL = $this->montaUpdate();
        if ($dbConfig->executaSql($sSQL)) {
            $retorno = true;
        } else {
            $retorno = false;
        }
        $dbConfig->fechaBd();

        return $retorno;
    }

    public function montaUpdate()
    {
        $sQuebra = "<br>";
        $sSQL  = " UPDATE ".$sQuebra;
        $sSQL .= "     sw_andamento_padrao ".$sQuebra;
        $sSQL .= " SET ".$sQuebra;
        $sSQL .= "     num_passagens 	= ".$this->numPassagens	.", ".$sQuebra;
        $sSQL .= "     cod_orgao 		= ".$this->codOrgao	.", ".$sQuebra;
        #$sSQL .= "     cod_unidade 		= ".$this->codUnidade.", ".$sQuebra;
        #$sSQL .= "     cod_departamento = ".$this->codDepartamento	.", ".$sQuebra;
        #$sSQL .= "     cod_setor 		= ".$this->codSetor.", ".$sQuebra;
        #$sSQL .= "     ano_exercicio 	= '".$this->anoE."', ".$sQuebra;
        $sSQL .= "     descricao 		= '".$this->descricao."', ".$sQuebra;
        $sSQL .= "     num_dia	 		= ".$this->numDia." ".$sQuebra;
        $sSQL .= " WHERE ".$sQuebra;
        $sSQL .= "     cod_classificacao = ".$this->classificacao." AND ".$sQuebra;
        $sSQL .= "     cod_assunto 		= ".$this->assunto." AND ".$sQuebra;
        $sSQL .= "     ordem 			= ".$this->ordem." ".$sQuebra;
        //echo $sSQL;
        $sSQL = str_replace("<br>", "", $sSQL);

        return $sSQL;
    }
/**************************************************************************/
/**** Faz o delete dos dados para andamento padrão                      ***/
/**************************************************************************/
    public function deleteAndamento()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "DELETE FROM sw_andamento_padrao WHERE cod_assunto = ".$this->assunto." AND cod_classificacao = ".$this->classificacao." AND cod_orgao = ".$this->codOrgao."
        AND cod_unidade = ".$this->codUnidade." AND cod_departamento = ".$this->codDepartamento."
        AND cod_setor = ".$this->codSetor." AND num_passagens = ".$this->numPassagens."
        AND ano_exercicio = '".$this->anoE."'";
        //print $insert;
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

/**************************************************************************/
/**** Faz a alteração do restante das ordens do Andaamento Padrão       ***/
/**************************************************************************/
    public function alteraOrdemExclui()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "UPDATE sw_andamento_padrao SET ordem = (ordem - 1) WHERE cod_assunto = ".$this->assunto." AND cod_classificacao = ".$this->classificacao." AND ordem >= ".$this->ordem;
        //print $insert;
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

/**************************************************************************/
/**** Faz a alteração do restante das ordens do Andaamento Padrão       ***/
/**************************************************************************/
    public function updateOrdem()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "UPDATE sw_andamento_padrao SET ordem = ".$this->valorAlteracao." WHERE cod_assunto = ".$this->assunto." AND cod_classificacao = ".$this->classificacao." AND cod_orgao = ".$this->codOrgao."
        AND cod_unidade = ".$this->codUnidade." AND cod_departamento = ".$this->codDepartamento."
        AND cod_setor = ".$this->codSetor." AND num_passagens = ".$this->numPassagens."
        AND ano_exercicio = '".$this->anoE."'";
        //print $insert;
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

/**************************************************************************/
/**** Faz a alteração ascendente das ordens do Andamento Padrão         ***/
/**************************************************************************/
    public function alteraOrdemAsc($posicaoAtual, $posicaoNova)
    {
        $sQuebra = "<br>";
        //CONDIÇÃO PARA O DELETE
        $sCondDel  = " WHERE ".$sQuebra;
        $sCondDel .= "     COD_CLASSIFICACAO = ".$this->classificacao." AND ".$sQuebra;
        $sCondDel .= "     COD_ASSUNTO = ".$this->assunto." AND ".$sQuebra;
        $sCondDel .= "     ORDEM = ".$posicaoAtual."; ".$sQuebra;
        $sSQL = $this->montaDelete( $sCondDel );
        //MOVE TODOS OS ELEMENTOS, COMPREENDIDOS ENTRE A POSIÇÃO ATUAL E A NOVA, PARA CIMA
        $sSQL .= " UPDATE ".$sQuebra;
        $sSQL .= "     sw_andamento_padrao ".$sQuebra;
        $sSQL .= " SET ".$sQuebra;
        $sSQL .= "     ORDEM = ( ORDEM + 1 ) ".$sQuebra;
        $sSQL .= " WHERE ".$sQuebra;
        $sSQL .= "     COD_CLASSIFICACAO = ".$this->classificacao." AND ".$sQuebra;
        $sSQL .= "     COD_ASSUNTO = ".$this->assunto." AND ".$sQuebra;
        $sSQL .= "     ORDEM >= ".$posicaoNova." AND ".$sQuebra;
        $sSQL .= "     ORDEM < ".$posicaoAtual."; ".$sQuebra;
        //SETA A ORDEM NOVA DO TRAMITE E INSERE ELE NA BASE NOVAMENTE
        $this->ordem = $posicaoNova;
        $sSQL .= $this->montaInsert();
        //echo $sSQL;
        $sSQL = str_replace("<br>", "", $sSQL );
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($dbConfig->executaSql($sSQL)) {
            $retorno = true;
        } else {
            $retorno = false;
        }
        $dbConfig->fechaBd();

        return $retorno;
    }

/**************************************************************************/
/**** Faz a alteração descendente das ordens do Andamento Padrão        ***/
/**************************************************************************/
    public function alteraOrdemDesc($posicaoAtual, $posicaoNova)
    {
        $sQuebra = "<br>";
        //CONDIÇÃO PARA O DELETE
        $sCondDel  = " WHERE ".$sQuebra;
        $sCondDel .= "     COD_CLASSIFICACAO = ".$this->classificacao." AND ".$sQuebra;
        $sCondDel .= "     COD_ASSUNTO = ".$this->assunto." AND ".$sQuebra;
        $sCondDel .= "     ORDEM = ".$posicaoAtual."; ".$sQuebra;
        $sSQL = $this->montaDelete( $sCondDel );
        //MOVE TODOS OS ELEMENTOS, COMPREENDIDOS ENTRE A POSIÇÃO ATUAL E A NOVA, PARA BAIXO
        $sSQL .= " UPDATE ".$sQuebra;
        $sSQL .= "     sw_andamento_padrao ".$sQuebra;
        $sSQL .= " SET ".$sQuebra;
        $sSQL .= "     ORDEM = ( ORDEM - 1 ) ".$sQuebra;
        $sSQL .= " WHERE ".$sQuebra;
        $sSQL .= "     COD_CLASSIFICACAO = ".$this->classificacao." AND ".$sQuebra;
        $sSQL .= "     COD_ASSUNTO = ".$this->assunto." AND ".$sQuebra;
        $sSQL .= "     ORDEM <= ".$posicaoNova." AND ".$sQuebra;
        $sSQL .= "     ORDEM > ".$posicaoAtual."; ".$sQuebra;
        //SETA A ORDEM NOVA DO TRAMITE E INSERE ELE NA BASE NOVAMENTE
        $this->ordem = $posicaoNova;
        $sSQL .= $this->montaInsert();
        //echo $sSQL;
        $sSQL = str_replace("<br>", "", $sSQL );
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($dbConfig->executaSql($sSQL)) {
            $retorno = true;
        } else {
            $retorno = false;
        }
        $dbConfig->fechaBd();

        return $retorno;
    }

/**************************************************************************/
/**** Monta o SQL para a exclusão de um andamento					        ***/
/**************************************************************************/
    public function montaDelete($stCondicao = "")
    {
        $sQuebra = "<br>";
        $sSQL  = " DELETE FROM ".$sQuebra;
        $sSQL .= "     sw_andamento_padrao ".$sQuebra;
        if ($stCondicao) {
            $sSQL .= $stCondicao;
        }
        //echo $sSQL;
        return $sSQL;
    }

/**************************************************************************/
/**** Monta o SQL para a inclusao de um  Andamento Padrão               ***/
/**************************************************************************/
    public function montaInsert()
    {
        $sQuebra = "<br>";
        $sSQL  = " INSERT INTO ".$sQuebra;
        $sSQL .= "     sw_andamento_padrao ( ".$sQuebra;
        $sSQL .= "     num_passagens, ".$sQuebra;
        $sSQL .= "     cod_classificacao, ".$sQuebra;
        $sSQL .= "     cod_assunto, ".$sQuebra;
        $sSQL .= "     cod_orgao, ".$sQuebra;
        #$sSQL .= "     cod_unidade, ".$sQuebra;
        #$sSQL .= "     cod_departamento, ".$sQuebra;
        #$sSQL .= "     cod_setor, ".$sQuebra;
        #$sSQL .= "     ano_exercicio, ".$sQuebra;
        $sSQL .= "     descricao, ".$sQuebra;
        $sSQL .= "     num_dia, ".$sQuebra;
        $sSQL .= "     ordem) ".$sQuebra;
        $sSQL .= " VALUES (".$sQuebra;
        $sSQL .= "     ".$this->numPassagens.", ".$sQuebra;
        $sSQL .= "     ".$this->classificacao.", ".$sQuebra;
        $sSQL .= "     ".$this->assunto.", ".$sQuebra;
        $sSQL .= "     ".$this->codOrgao.", ".$sQuebra;
        #$sSQL .= "     ".$this->codUnidade.", ".$sQuebra;
        #$sSQL .= "     ".$this->codDepartamento.", ".$sQuebra;
        #$sSQL .= "     ".$this->codSetor.", ".$sQuebra;
        #$sSQL .= "     '".$this->anoE."', ".$sQuebra;
        $sSQL .= "     '".$this->descricao."', ".$sQuebra;
        $sSQL .= "     ".$this->numDia.", ".$sQuebra;
        $sSQL .= "     ".$this->ordem.") ".$sQuebra;
        //echo $sSQL;
        return $sSQL;
    }

/**************************************************************************/
/**** Executa consulta no banco conforme os parâmetros passados          ***/
/**************************************************************************/
    public function consultaAndamento(&$arAndamento, $stCondicao = "", $stOrdem = "")
    {
        $sSQL = $this->montaConsultaAndamento($stCondicao , $stOrdem );
        $sSQL = str_replace("<br>", "", $sSQL);
        $arAndamento = array();
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($dbConfig->abreSelecao($sSQL)) {
            $dbConfig->vaiPrimeiro();
            while (!$dbConfig->eof()) {
                $arAndamento[] = array(
                                        'num_passagens' => $dbConfig->pegaCampo('num_passagens'),
                                        'cod_classificacao' => $dbConfig->pegaCampo('cod_classificacao'),
                                        'cod_assunto' => $dbConfig->pegaCampo('cod_assunto'),
                                        'cod_orgao' => $dbConfig->pegaCampo('cod_orgao'),
                                        'cod_unidade' => $dbConfig->pegaCampo('cod_unidade'),
                                        'cod_departamento' => $dbConfig->pegaCampo('cod_departamento'),
                                        'cod_setor' => $dbConfig->pegaCampo('cod_setor'),
                                        'ano_exercicio' => $dbConfig->pegaCampo('ano_exercicio'),
                                        'descricao' => $dbConfig->pegaCampo('descricao'),
                                        'num_dia' => $dbConfig->pegaCampo('num_dia'),
                                        'ordem' => $dbConfig->pegaCampo('ordem')
                                        );
                $dbConfig->vaiProximo();
            }
            $retorno = true;
        } else {
            $retorno = false;
        }
        $dbConfig->fechaBd();

        return $retorno;
    }
/**************************************************************************/
/**** Monta o SQL para a consulata do andamento padrao                  ***/
/**************************************************************************/
    public function montaConsultaAndamento($stCondicao = "", $stOrdem = "")
    {
        $sQuebra = "<br>";
        $sSQL  = " SELECT ".$sQuebra;
        $sSQL .= "     num_passagens,".$sQuebra;
        $sSQL .= "     cod_classificacao,".$sQuebra;
        $sSQL .= "     cod_assunto,".$sQuebra;
        $sSQL .= "     cod_orgao,".$sQuebra;
        $sSQL .= "     cod_unidade,".$sQuebra;
        $sSQL .= "     cod_departamento,".$sQuebra;
        $sSQL .= "     cod_setor,".$sQuebra;
        $sSQL .= "     ano_exercicio,".$sQuebra;
        $sSQL .= "     descricao,".$sQuebra;
        $sSQL .= "     num_dia,".$sQuebra;
        $sSQL .= "     ordem".$sQuebra;
        $sSQL .= " FROM".$sQuebra;
        $sSQL .= "     sw_andamento_padrao ".$sQuebra;
        if ($stCondicao) {
            $sSQL .= $stCondicao;
        }
        if ($stOrdem) {
            $sSQL .= $stOrdem;
        }
        //echo $sSQL;
        return $sSQL;
    }

/**************************************************************************/
/**** Recupera todos os registros conforme a condição com os nomes dos  ***/
/**** contidos nos campos das tabelas relacionadas, retornando os       ***/
/**** registros em um array que deve ser passado por referência         ***/
/**************************************************************************/
    public function recuperaCompleto(&$arAndamento, $stCondicao = "", $stOrdem = "")
    {
        $sSQL = $this->montaRecuperaCompleto($stCondicao , $stOrdem );
        $sSQL = str_replace("<br>", "", $sSQL);
        //echo $sSQL;
        $arAndamento = array();
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($dbConfig->abreSelecao($sSQL)) {

            $dbConfig->vaiPrimeiro();
            while (!$dbConfig->eof()) {
                $arAndamento[] = array(
                                        'num_passagens' => $dbConfig->pegaCampo('num_passagens'),
                                        'cod_classificacao' => $dbConfig->pegaCampo('cod_classificacao'),
                                        'nom_classificacao' => $dbConfig->pegaCampo('nom_classificacao'),
                                        'cod_assunto' => $dbConfig->pegaCampo('cod_assunto'),
                                        'nom_assunto' => $dbConfig->pegaCampo('nom_assunto'),
                                        'cod_orgao' => $dbConfig->pegaCampo('cod_orgao'),
                                        'nom_orgao' => $dbConfig->pegaCampo('nom_orgao'),
                                        'cod_unidade' => '',
                                        'nom_unidade' => '',
                                        'cod_departamento' => '',
                                        'nom_departamento' => '',
                                        'cod_setor' => '',
                                        'nom_setor' => '',
                                        'ano_exercicio' => '',
                                        'descricao' => $dbConfig->pegaCampo('descricao'),
                                        'num_dia' => $dbConfig->pegaCampo('num_dia'),
                                        'ordem' => $dbConfig->pegaCampo('ordem')
                                        );
                $dbConfig->vaiProximo();
            }
            $retorno = true;
        } else {
            $retorno = false;
        }
        $dbConfig->fechaBd();

        return $retorno;
    }

/**************************************************************************/
/**** Monta o SQL para o método recuperaCompleto                        ***/
/**************************************************************************/
    public function montaRecuperaCompleto($stCondicao = "", $stOrdem = "")
    {
        $sQuebra = "<br>";
        /*
    $sSQL  = " SELECT ".$sQuebra;
        $sSQL .= "    andamento.num_passagens, ".$sQuebra;
        $sSQL .= "    andamento.cod_classificacao, ".$sQuebra;
        $sSQL .= "    andamento.cod_assunto, ".$sQuebra;
        $sSQL .= "    andamento.cod_orgao, ".$sQuebra;
        $sSQL .= "    andamento.cod_unidade, ".$sQuebra;
        $sSQL .= "    andamento.cod_departamento, ".$sQuebra;
        $sSQL .= "    andamento.cod_setor, ".$sQuebra;
        $sSQL .= "    andamento.ano_exercicio, ".$sQuebra;
        $sSQL .= "    andamento.descricao, ".$sQuebra;
        $sSQL .= "    andamento.num_dia, ".$sQuebra;
        $sSQL .= "    andamento.ordem, ".$sQuebra;
        $sSQL .= "    orgao.nom_orgao, ".$sQuebra;
        $sSQL .= "    unidade.nom_unidade, ".$sQuebra;
        $sSQL .= "    departamento.nom_departamento, ".$sQuebra;
        $sSQL .= "    setor.nom_setor, ".$sQuebra;
        $sSQL .= "    classificacao.nom_classificacao, ".$sQuebra;
        $sSQL .= "    assunto.nom_assunto ".$sQuebra;
        $sSQL .= " FROM ".$sQuebra;
        $sSQL .= "    sw_andamento_padrao AS andamento, ".$sQuebra;
        $sSQL .= "    sw_classificacao as classificacao, ".$sQuebra;
        $sSQL .= "    sw_assunto as assunto, ".$sQuebra;
        $sSQL .= "    administracao.orgao as orgao, ".$sQuebra;
        $sSQL .= "    administracao.unidade as unidade, ".$sQuebra;
        $sSQL .= "    administracao.departamento as departamento, ".$sQuebra;
        $sSQL .= "    administracao.setor as setor ".$sQuebra;
        $sSQL .= " WHERE ".$sQuebra;
        $sSQL .= "    andamento.cod_orgao = orgao.cod_orgao AND ".$sQuebra;
        $sSQL .= "    andamento.ano_exercicio = orgao.ano_exercicio AND ".$sQuebra;
        $sSQL .= "    andamento.cod_unidade = unidade.cod_unidade  AND ".$sQuebra;
        $sSQL .= "    andamento.ano_exercicio = unidade.ano_exercicio AND ".$sQuebra;
        $sSQL .= "    andamento.cod_departamento = departamento.cod_departamento  AND ".$sQuebra;
        $sSQL .= "    andamento.ano_exercicio = departamento.ano_exercicio AND ".$sQuebra;
        $sSQL .= "    andamento.cod_setor = setor.cod_setor  AND ".$sQuebra;
        $sSQL .= "    andamento.ano_exercicio = setor.ano_exercicio AND ".$sQuebra;
        $sSQL .= "    andamento.cod_classificacao = classificacao.cod_classificacao AND ".$sQuebra;
        $sSQL .= "    andamento.cod_assunto = assunto.cod_assunto AND ".$sQuebra;
        $sSQL .= "    classificacao.cod_classificacao = assunto.cod_classificacao ".$sQuebra;
                                                    */

        $sSQL  = " SELECT andamento.num_passagens                                              \n";
        $sSQL .= "      , andamento.cod_classificacao                                          \n";
        $sSQL .= "      , andamento.cod_assunto                                                \n";
        $sSQL .= "      , andamento.cod_orgao                                                  \n";
        $sSQL .= "      , andamento.descricao                                                  \n";
        $sSQL .= "      , andamento.num_dia                                                    \n";
        $sSQL .= "      , andamento.ordem                                                      \n";
        $sSQL .= "      , recuperaDescricaoOrgao(orgao.cod_orgao, now()::date) as nom_orgao    \n";
        $sSQL .= "      , classificacao.nom_classificacao                                      \n";
        $sSQL .= "      , assunto.nom_assunto                                                  \n";
        $sSQL .= "   FROM sw_andamento_padrao AS andamento                                     \n";
        $sSQL .= "      , sw_classificacao as classificacao                                    \n";
        $sSQL .= "      , sw_assunto as assunto                                                \n";
        $sSQL .= "      , organograma.orgao                                                    \n";
        $sSQL .= "  WHERE andamento.cod_orgao = orgao.cod_orgao                                \n";
        $sSQL .= "    AND andamento.cod_classificacao = classificacao.cod_classificacao        \n";
        $sSQL .= "    AND andamento.cod_assunto = assunto.cod_assunto                          \n";
        $sSQL .= "    AND classificacao.cod_classificacao = assunto.cod_classificacao          \n";

        if ($stCondicao) {
            $sSQL .= $stCondicao;
        }
        if ($stOrdem) {
            $sSQL .= $stOrdem;
        }

        return $sSQL;
    }
/**************************************************************************/
/**** Altera o número de passagens quando é removido ou alterado        ***/
/**** um dos tramites                                                   ***/
/**************************************************************************/
    public function corrigeNumPassagem($sCondicao)
    {
        $sSQL = $this->montaCorrigeNumPassagem($sCondicao);
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($dbConfig->executaSql($sSQL)) {
            $retorno = true;
        } else {
            $retorno = false;
        }
        $dbConfig->fechaBd();

        return $retorno;
    }
/**************************************************************************/
/**** Monta o SQL para o método corrigeNumPassagem                      ***/
/**************************************************************************/
    public function montaCorrigeNumPassagem($sCondicao)
    {
        $sQuebra = "<br>";
        $sSQL  = " UPDATE sw_andamento_padrao ".$sQuebra;
        $sSQL .= " SET ".$sQuebra;
        $sSQL .= "     num_passagens = ( num_passagens - 1 ) ".$sQuebra;
        $sSQL .= $sCondicao;
        //echo $sSQL;
        $sSQL = str_replace("<br>", "", $sSQL);

        return $sSQL;
    }

/***************************************************************************/
/**** União dos métodos corrigeNumPassagem e update para executar tudo   ***/
/**** em uma única transação                                             ***/
/***************************************************************************/
    public function alteraAndamentoCompleto($numPassagens)
    {
        $sSQL  = $this->montaUpdate();
        $sSQL .= ";";
        $sSQL .= $this->montaCorrigeNumPassagem($numPassagens);

        //echo $sSQL."<br>";
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($dbConfig->executaSql($sSQL)) {
            $retorno = true;
        } else {
            $retorno = false;
        }
        $dbConfig->fechaBd();

        return $retorno;
    }

/**************************************************************************/
/**** Pega as variáveis de ação do usuário                              ***/
/**************************************************************************/
    public function deletaAndamentoCompleto($condDelete, $condNumPassagens, $ordem)
    {
        $sQuebra = "<br>";
        $sSQL  = $this->montaDelete( $condDelete );
        $sSQL .= ";".$sQuebra;
        $sSQL .= $this->montaCorrigeNumPassagem( $condNumPassagens );
        $sSQL .= ";".$sQuebra;
        //MOVE TODOS OS ELEMENTOS, COMPREENDIDOS ENTRE A POSIÇÃO ATUAL E A NOVA, PARA BAIXO
        $sSQL .= " UPDATE ".$sQuebra;
        $sSQL .= "     sw_andamento_padrao ".$sQuebra;
        $sSQL .= " SET ".$sQuebra;
        $sSQL .= "     ORDEM = ( ORDEM - 1 ) ".$sQuebra;
        $sSQL .= " WHERE ".$sQuebra;
        $sSQL .= "     COD_CLASSIFICACAO = ".$this->classificacao." AND ".$sQuebra;
        $sSQL .= "     COD_ASSUNTO = ".$this->assunto." AND ".$sQuebra;
        $sSQL .= "     ORDEM > ".$ordem."; ".$sQuebra;
        $sSQL = str_replace("<br>", "", $sSQL);
        //echo $sSQL;
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($dbConfig->executaSql($sSQL)) {
            $retorno = true;
        } else {
            $retorno = false;
        }
        $dbConfig->fechaBd();

        return $retorno;
    }

/**************************************************************************/
/**** Pega as variáveis de ação do usuário                              ***/
/**************************************************************************/
    public function setaValorAlteracao($valorAlteracao)
    {
    $this->valorAlteracao = $valorAlteracao;
    }

/**************************************************************************/
/**** Pega as variáveis de ação do usuário                              ***/
/**************************************************************************/
    public function setaValorTipoProcesso($codAssunto,$codClassificacao)
    {
    $this->assunto = $codAssunto;
    $this->classificacao = $codClassificacao;
    }

/**************************************************************************/
/**** Faz o delete dos dados para andamento padrão completo             ***/
/**************************************************************************/
    public function deleteAndamentoPadrao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert =   "DELETE
                    FROM
                    sw_andamento_padrao
                    WHERE
                    cod_assunto = ".$this->assunto." AND
                    cod_classificacao = ".$this->classificacao." AND
                    cod_orgao = ".$this->codOrgao." AND
                    cod_unidade = ".$this->codUnidade." AND
                    cod_departamento = ".$this->codDepartamento." AND
                    cod_setor = ".$this->codSetor." AND
                    ano_exercicio = '".$this->anoE."'";
        //print $insert;
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }
/**************************************************************************/
/**** Pega as variáveis de Alterar Encaminhamento
/**************************************************************************/
    public function setaAndamentoPadraoi($codProcesso, $codAndamento, $codOrgao, $codUnidade, $codDepartamento, $codSetor)
    {
        $this->codProcesso = $codProcesso;
        $this->codAndamento = $codAndamento;
        $this->codOrgao = $codOrgao;
        $this->codUnidade = $codUnidade;
        $this->codDepartamento = $codDepartamento;
        $this->codSetor = $codSetor;
        }

/**************************************************************************/
/**** Pega o Ultimo Andamento                                           ***/
/**************************************************************************/
    public function ultimoAndamento()
    {
        $sSQL = "SELECT cod_andamento FROM sw_ultimo_andamento  WHERE cod_processo = ".$this->codProcesso." AND ano_exercicio = '".$this->anoE."'";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $codAndamentoMax  = trim($dbEmp->pegaCampo("cod_andamento"));
        $soma = $dbEmp->numeroDeLinhas;
        if ($soma != 0)
            return $codAndamentoMax;
        else
            return false;
            }
/**************************************************************************/
/**** Faz o update de Alterar Encaminhamento
/**************************************************************************/
    public function cancelaEncaminhamento()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
$insert = "select
           cod_processo_pai
         , cod_processo_filho
         , exercicio_pai
         , exercicio_filho
        from
           sw_processo_apensado
        where
           cod_processo_pai = ".$this->codProcesso."
           and timestamp_desapensamento is null;";
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($insert);
        $conn->vaiPrimeiro();
        $registros = $conn->numeroDeLinhas;
//Rotina para inserir os filhos(apensados)
if ($registros > 0) {
    for ($i = 1; $i <= $registros; $i++) {
       $codProcesso_pai   = $conn->pegaCampo("cod_processo_pai");
       $codProcesso_filho = $conn->pegaCampo("cod_processo_filho");
       $exercicio_pai     = $conn->pegaCampo("exercicio_pai");
       $exercicio_filho   = $conn->pegaCampo("exercicio_filho");

       $codAndamento = pegaID("cod_andamento","sw_andamento","Where cod_processo = '$codProcesso_filho' And ano_exercicio = '$exercicio_filho'");
$codAndamento = $codAndamento - 1;

        $insert .= "DELETE FROM sw_andamento WHERE cod_processo = ".$codProcesso_filho." AND cod_andamento = ".$codAndamento." AND ano_exercicio = '".$exercicio_filho."';
        UPDATE sw_processo SET cod_situacao = 3 WHERE cod_processo = ".$codProcesso_filho." AND ano_exercicio = '".$exercicio_filho."';";
$conn->vaiProximo();
}
}
        $insert .= "DELETE FROM sw_andamento WHERE cod_processo = ".$this->codProcesso." AND cod_andamento = ".$this->codAndamento." AND ano_exercicio = '".$this->anoE."';
        UPDATE sw_processo SET cod_situacao = 3 WHERE cod_processo = ".$this->codProcesso." AND ano_exercicio = '".$this->anoE."';";

        //print $insert;
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

//*************************************************************************
}
