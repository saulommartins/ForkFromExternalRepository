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
    * Classe situação, é feito o cadastro, alterações e excluisões das situações dos bens
    * Data de Criação   : 25/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 12234 $
    $Name$
    $Autor: $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.01.10
*/

/*
$Log$
Revision 1.8  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:11:27  diego

*/

    class situacao
    {
        /*** Variáveis da classe ***/
            var $codigo;
            var $nome;

        /*** Método Construtor ***/
            function situacao()
            {
                $this->codigo = "";
                $this->nome = "";
            }

        /*** Método que seta Variáveis ***/
            function setaVariaveis($cod,$nom)
            {
                $this->codigo = $cod;
                $this->nome = $nom;
            }

            function teste()
            {
                echo $this->codigo;
                echo $this->nome;
            }

        /*** Método que gera o código da situação de bens ***/
            function geraCodigo()
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select max(cod_situacao) as cod_situacao from patrimonio.situacao_bem";
                $dbConfig->abreSelecao($select);

                $this->codigo = $dbConfig->pegaCampo("cod_situacao");

                $this->codigo = $this->codigo + 1; //Pega o valor do ultimo codigo e soma mais 1
                $dbConfig->limpaSelecao();
                $dbConfig->fechaBd();

            }

        /*** Método que inclui uma situação ***/
            function incluiSituacao()
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $insert =   "insert into patrimonio.situacao_bem (cod_situacao, nom_situacao)
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

        /*** Método que lista as situações do bem ***/
            function listaSituacao()
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select = "select cod_situacao, nom_situacao from patrimonio.situacao_bem order by nom_situacao";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_situacao");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_situacao");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que mostra os dados da situação dos bens para serem alteradas ***/
            function mostraSituacao($cod)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_situacao, nom_situacao
                                from patrimonio.situacao_bem
                                where cod_situacao = '$cod'";
                $dbConfig->abreSelecao($select);
                $this->codigo = $dbConfig->pegaCampo("cod_situacao");
                $this->nome = $dbConfig->pegaCampo("nom_situacao");
                $dbConfig->limpaSelecao();
                $dbConfig->fechaBd();
            }

        /*** Método que Altera os dados da situação de bens ***/
            function alteraSituacao()
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $update = "update
                                patrimonio.situacao_bem
                            set
                                cod_situacao = $this->codigo,
                                nom_situacao = '$this->nome'
                            where
                                cod_situacao = $this->codigo";
                $result = $dbConfig->executaSql($update);
                if ($result) {
                    return true;
                    $dbConfig->fechaBd();
                } else {
                    return false;
                    $dbConfig->fechaBd();
                }
            }

        /*** Método que exclui a situação do bem ***/
            function excluiSituacao()
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $delete =   "delete from patrimonio.situacao_bem
                                where cod_situacao = $this->codigo";
                $result = $dbConfig->executaSql($delete);
                if ($result != "") {
                    return true;
                    $dbConfig->fechaBd();
                } else {
                    return false;
                    $dbConfig->fechaBd();
                }
            }
    }
