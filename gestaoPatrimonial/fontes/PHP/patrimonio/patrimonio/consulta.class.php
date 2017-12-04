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
    * Classe de consulta de bens
    * Data de Criação   : 27/03/2003

    * @author Desenvolvedor Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 12234 $
    $Name$
    $Autor: $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.7  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:11:27  diego

*/

    class consulta
    {
        /*** Declaração das variáveis da classe ***/
            var $codigo;
            var $descricao;
            var $natureza;
            var $grupo;
            var $especie;
            var $local;
            var $setor;
            var $departamento;
            var $unidade;
            var $orgao;

        /*** Método construtor ***/
            function consulta()
            {
                $this->codigo = 0;
                $this->natureza = "";
                $this->grupo = "";
                $this->especie = "";
                $this->local = "";
                $this->setor = "";
                $this->departamento = "";
                $this->unidade = "";
                $this->orgao = "";
            }

        /*** Método que seta variáveis ***/
            function setaVariaveisBens($cod, $natu, $grup, $espec, $org, $uni, $set, $loc)
            {
                $this->codigo = $cod;
                $this->natureza = $natu;
                $this->grupo = $grup;
                $this->especie = $espec;
                $this->orgao = $org;
                $this->unidade = $uni;
                $this->setor = $set;
                $this->local = $loc;
            }

        /*** Método que gera a consulta de acordo com o código selecionado ***/
            function consultaCodBem()
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_bem, descricao
                                from patrimonio.bem
                                where cod_bem = $this->codigo";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_bem");
                    $lista[$cod] = $dbConfig->pegaCampo("descricao");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que mostra a natureza ***/
            function mostraNatureza()
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_natureza, nom_natureza
                                from patrimonio.natureza";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_natureza");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_natureza");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que mostra a Grupo ***/
            function mostraGrupo($natu)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_grupo, nom_grupo
                                from patrimonio.grupo
                                where cod_natureza = '$natu'";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_grupo");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_grupo");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que mostra a espécie ***/
            function mostraEspecie($natu, $grup)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_especie, nom_especie
                                from patrimonio.especie
                                where cod_natureza = '$natu'
                                and cod_grupo = '$grup'";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_especie");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_especie");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que mostra o Orgao ***/
            function mostraOrgao()
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_orgao, nom_orgao, ano_exercicio
                                from administracao.orgao
                                WHERE cod_orgao > 0
                                ORDER by nom_orgao";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_orgao");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_orgao");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

           /*** Método que mostra a Unidade ***/
            function mostraUnidade($org)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_unidade, nom_unidade
                                from administracao.unidade
                                where cod_orgao = '$org'
                                AND cod_unidade > 0
                                ORDER by nom_unidade";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_unidade");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_unidade");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

          /*** Método que mostra a Depto ***/
            function mostraDepto($org,$uni)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_departamento, nom_departamento
                                from administracao.departamento
                                where cod_orgao = '$org'
                                AND cod_unidade = '$uni'
                                AND cod_departamento > 0
                                ORDER by nom_departamento";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_departamento");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_departamento");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que mostra a Depto ***/
            function mostraSetor($org,$uni,$dep)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_setor, nom_setor
                                from administracao.setor
                                where cod_orgao = '$org'
                                AND cod_unidade = '$uni'
                                AND cod_departamento = '$dep'
                                AND cod_setor > 0
                                ORDER by nom_setor";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_setor");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_setor");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

            /*** Método que mostra o local ***/
            function mostraLocal($org,$uni,$dep,$set)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                $select =   "select cod_local, nom_local
                                from administracao.local
                                where cod_orgao = '$org'
                                AND cod_unidade = '$uni'
                                AND cod_departamento = '$dep'
                                AND cod_setor = '$set'
                                AND cod_local > 0
                                ORDER by nom_local";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $cod = $dbConfig->pegaCampo("cod_local");
                    $lista[$cod] = $dbConfig->pegaCampo("nom_local");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que realiza a busca por classificação ***/
            function consultaClass()
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                if ($this->natureza != "" && $this->grupo != 0 && $this->especie != 0) {
                    $select =   "select distinct bem.cod_bem, especie.nom_especie
                                    from patrimonio.bem as bem, patrimonio.bem_atributo_especie as bem_atributo_especie, patrimonio.especie as especie, patrimonio.especie_atributo as especie_atributo
                                    where bem.cod_bem = bem_atributo_especie.cod_bem
                                    and bem_atributo_especie.cod_especie = especie_atributo.cod_especie
                                    and especie.cod_especie = especie_atributo.cod_especie
                                    and bem_atributo_especie.cod_natureza = '$this->natureza'
                                    and bem_atributo_especie.cod_grupo = '$this->grupo'
                                    and bem_atributo_especie.cod_especie = '$this->especie'";
                    echo $select;
                    $dbConfig->abreSelecao($select);
                    while (!$dbConfig->eof()) {
                        $cod = $dbConfig->pegaCampo("cod_bem");
                        $lista[$cod] = $dbConfig->pegaCampo("nom_especie");
                        $dbConfig->vaiProximo();
                    }
                    $dbConfig->limpaSelecao();

                    return $lista;
                    $dbConfig->fechaBd();
                }
            }
    }

?>
