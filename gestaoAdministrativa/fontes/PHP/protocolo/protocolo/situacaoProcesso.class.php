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
* Classe de negócio SituacaoProcesso
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3185 $
$Name$
$Author: lizandro $
$Date: 2005-11-30 17:27:29 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.06.98
*/

    class situacaoProcesso
    {
        /*** Declaração das variáveis ***/
        var $codigo;
        var $nome;
        var $historicoArquivamento;
        var $anoE;
        var $localizacaoFisica;

        /*** Método Construtor ***/
        function situacaoProcesso()
        {
            $this->codigo = 0;
            $this->nome = "";
            $this->historicoArquivamento = "";
            $this->anoE = "";
        }

        /*** Método que seta Variáveis ***/
        function setaVariaveis($nom)
        {
            $this->nome = $nom;
        }

        /*** Método que gera o código do tipo de processo ***/
        function geraCodigo()
        {
            $this->codigo = pegaID('cod_situacao',"sw_situacao_processo");
        }

        /*** Método que inclui tipo de processo ***/
        function incluiSituacaoProcesso()
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
                if (isset($this->nome)) {
                    $this->geraCodigo();
                    $insert =   "insert into sw_situacao_processo (cod_situacao, nom_situacao)
                                    values ($this->codigo, '$this->nome')"; //insere os dados na tabela
                    $result = $dbConfig->executaSql($insert);
                    if ($result) {
                        return true;
                        $dbConfig->fechaBd();
                    } else {
                        return false;
                        $dbConfig->fechaBd();
                    }
                }
        }

        function listaSituacaoProcesso()
        {
            $dbSit = new dataBaseLegado;
            $dbSit->abreBd();
            $select =   "select cod_situacao, nom_situacao
                            from sw_situacao_processo order by lower(nom_situacao)";
            $dbSit->abreSelecao($select);
            while (!$dbSit->eof()) {
                $cod = $dbSit->pegaCampo("cod_situacao");
                $lista[$cod] = $dbSit->pegaCampo("nom_situacao");
                $dbSit->vaiProximo();
            }
            $dbSit->limpaSelecao();

            return $lista;
            $dbSit->fechaBd();
        }

        function mostraSituacaoProcesso($cod)
        {
            $dbSit = new dataBaseLegado;
            $dbSit->abreBd();
            $select =   "select cod_situacao, nom_situacao
                            from sw_situacao_processo where cod_situacao = '$cod'";
            $dbSit->abreSelecao($select);
            if (!$dbSit->eof()) {
                $this->nome = $dbSit->pegaCampo("nom_situacao");
                $dbSit->limpaSelecao();
            }
            $dbSit->fechaBd();
        }

        function alteraSituacaoProcesso($codigo)
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
                    $update =   "update sw_situacao_processo set
                                    nom_situacao = '$this->nome'
                                    where cod_situacao = $codigo"; //altera os dados na tabela
                    $result = $dbConfig->executaSql($update);
                    if ($result) {
                        return true;
                        $dbConfig->fechaBd();
                    } else {
                        return false;
                        $dbConfig->fechaBd();
                    }
        }
        function excluiSituacaoProcesso($codigo)
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $delete =   "delete from sw_situacao_processo
                            where cod_situacao = $codigo";
            echo $delete."<br>";
            $result = $dbConfig->executaSql($delete);
            if ($result) {
                $dbConfig->fechaBd();

                return true;
            } else {
                $dbConfig->fechaBd();

                return false;
            }
        }

        function setaVariaveisArquivamento($cod,$nom,$historicoArquivamento,$anoE,$stLocalizacaoFisica)
        {
                $this->codigo = $cod;
                $this->nome = $nom;
                $this->historicoArquivamento = $historicoArquivamento;
                $this->anoE = $anoE;
                $this->localizacaoFisica = $stLocalizacaoFisica;
            }

        function insereArquivamento()
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();

            $texto_complementar = $_POST["textoComplementar"];

            $inNumCgm = Sessao::read('numCgm');

            $insert1 = "INSERT INTO sw_processo_arquivado (cod_processo,ano_exercicio,cod_historico,texto_complementar,cgm_arquivador,localizacao) VALUES ('".$this->nome."','".$this->anoE."','".$this->historicoArquivamento."','".$texto_complementar."', $inNumCgm, '".$this->localizacaoFisica."'); ";
            $insert2 = "UPDATE sw_processo SET cod_situacao = $this->codigo WHERE cod_processo = $this->nome and ano_exercicio = '".$this->anoE."'";
            $insert = $insert1.$insert2;

            if ($dbConfig->executaSql($insert))
                return true;
            else
                return false;
            $dbConfig->fechaBd();
        }

        function apagaArquivamento()
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $insert1 = "DELETE FROM sw_processo_arquivado
                        WHERE cod_processo = $this->nome
                        And ano_exercicio = '".$this->anoE."'
                        ; ";
            $insert2 = "UPDATE sw_processo SET cod_situacao = $this->codigo
                        WHERE cod_processo = $this->nome
                        And ano_exercicio = '".$this->anoE."'
                        ; ";
            $insert = $insert1.$insert2;

            if ($dbConfig->executaSql($insert))
                $ok = true;
            else
                $ok = false;
            $dbConfig->fechaBd();

            return $ok;
        }// fim da function

    }//fim da classe

?>
