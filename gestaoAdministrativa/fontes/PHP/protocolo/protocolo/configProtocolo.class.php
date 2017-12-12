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
* Classe de negócio ConfigProtocolo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3157 $
$Name$
$Author: pablo $
$Date: 2005-11-30 14:18:08 -0200 (Qua, 30 Nov 2005) $

Casos de uso: uc-01.06.91
*/

class configProtocolo
{
/**************************************************************************/
/**** Declaração das variáveis                                          ***/
/**************************************************************************/
    public $codClassificacao;
    public $nomClassificacao;
    public $codigo;
    public $nome;

/**************************************************************************/
/**** Método Construtor                                                 ***/
/**************************************************************************/
    public function configProtocolo()
    {
        $this->codClassificacao = "";
        $this->nomClassificacao = "";
        $this->codigo = "";
        $this->nome = "";

        }

/**************************************************************************/
/**** Pega as variáveis de ação do usuário                              ***/
/**************************************************************************/
    public function setaVariaveisClassificacao($codClassificacao,$nomClassificacao="")
    {
        $this->codClassificacao = $codClassificacao;
        $this->nomClassificacao = $nomClassificacao;
        }

/**************************************************************************/
/**** Setagem das variáveis                                             ***/
/**************************************************************************/
            function setaVariaveis($cod, $nom="")
            {
                $this->codigo = $cod;
                $this->nome = $nom;
            }
/**************************************************************************/
/**** Método que faz a inlusão  dos historicos                        ***/
/**************************************************************************/
            function incluiHistoricoArquivamento()
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $insert =   "insert into sw_historico_arquivamento (cod_historico, nom_historico)
                                values ($this->codigo, '$this->nome')";
                $result = $dbConfig->executaSql($insert);
                if ($result != "") {
                    return true;
                    $dbConfig->fechaBd();
                } else {
                    return false;
                    $dbConfig->fechaBd();
                }
            }

/**************************************************************************/
/**** Método que faz a Listagem  dos Hisoricos                          ***/
/**************************************************************************/
            function listaHistoricoArquivamento()
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_historico, nom_historico
                        from sw_historico_arquivamento";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_historico");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_historico");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

/**************************************************************************/
/**** Método que faz a Mostra de um único combustivel                   ***/
/**************************************************************************/
            function mostraHistoricoArquivamento($cod)
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_historico, nom_historico
                                from sw_historico_arquivamento
                                where cod_historico = '$cod'";
                $dbConfig->abreSelecao($select);
                $this->codigo = $dbConfig->pegaCampo("cod_historico");
                $this->nome = $dbConfig->pegaCampo("nom_historico");
                $dbConfig->limpaSelecao();
                $dbConfig->fechaBd();
            }

/**************************************************************************/
/**** Método que faz a alteração dos historicos                       ***/
/**************************************************************************/
            function alteraHistoricoArquivamento()
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $update =   "update sw_historico_arquivamento
                            set nom_historico = '$this->nome'
                            where cod_historico = $this->codigo";
                //echo $update;
                $result = $dbConfig->executaSql($update);
                if ($result) {
                    return true;
                    $dbConfig->fechaBd();
                } else {
                    return false;
                    $dbConfig->fechaBd();
                }
            }

/**************************************************************************/
/**** Método que faz a deleção dos Historicos                         ***/
/**************************************************************************/
            function excluiHistoricoArquivamento()
            {
                $dbConfig = new databaseLegado;
                $dbConfig->abreBd();
                $delete =   "delete from sw_historico_arquivamento
                       where cod_historico = $this->codigo";
                $result = $dbConfig->executaSql($delete);
                if ($result != "") {
                    return true;
                    $dbConfig->fechaBd();
                } else {
                    return false;
                    $dbConfig->fechaBd();
                }
            }

/**************************************************************************/
/**** Insere na tabela Classificacao
/**************************************************************************/
    public function insereClassificacao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "INSERT INTO sw_classificacao (cod_classificacao , nom_classificacao) VALUES ('".$this->codClassificacao."', '".$this->nomClassificacao."')";
        //print $insert;
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

/**************************************************************************/
/**** Altera na tabela CLASSIFICACAO
/**************************************************************************/
    public function alteraClassificacao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "UPDATE sw_classificacao SET nom_classificacao = '".$this->nomClassificacao."' WHERE cod_classificacao = '".$this->codClassificacao."'";
        //print $insert;
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

/**************************************************************************/
/**** Exclui na tabela CLASSIFICACAO
/**************************************************************************/
    public function excluiClassificacao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "DELETE FROM sw_classificacao WHERE cod_classificacao = '".$this->codClassificacao."'";
        //print $insert;
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }
}
