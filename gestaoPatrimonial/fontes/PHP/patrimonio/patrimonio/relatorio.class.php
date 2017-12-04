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
    * Classe de geração de relatórios
    * Data de Criação   : 01/04/2003

    * @author Desenvolvedor  Ricardo Lopes de Alencar
    * @author Desenvolvedor  Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 18535 $
    $Name$
    $Autor: $
    $Date: 2006-12-06 10:42:47 -0200 (Qua, 06 Dez 2006) $

    * Casos de uso: uc-03.01.09
                    uc-03.01.13
*/

/*
$Log$
Revision 1.13  2006/12/06 12:42:47  larocca
Bug #6943#

Revision 1.12  2006/11/28 15:48:08  larocca
Bug #6936

Revision 1.11  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.10  2006/07/06 12:11:27  diego

*/

    class relatorio
    {
        /*** Variáveis da classe ***/
            var $codNatureza;
            var $codGrupo;
            var $codEspecie;
            var $sqlPaginacao;
            var $sSQLListaTipoRelatorio;

        /*** Método Construtor ***/
            function relatorio()
            {
                $this->codNatureza = "";
                $this->codGrupo = "";
                $this->codEspecie = "";
                $this->sqlPaginacao = "";
                $this->sSQLListaTipoRelatorio = "";
            }

        /*** Método que lista as naturezas para a geração do relatório ***/
            function listaNatureza()
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                if ($this->codNatureza != "") {
                    $select =   "select cod_natureza, nom_natureza
                                    from patrimonio.natureza
                                    where cod_natureza = '$this->codNatureza'";
                } else {
                    $select =   "select cod_natureza, nom_natureza
                                    from patrimonio.natureza";
                }
                //echo $select."<br>";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $codN = $dbConfig->pegaCampo("cod_natureza");
                    $lista[$codN] = $dbConfig->pegaCampo("nom_natureza");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que lista os grupos para a geração do relatório ***/
            function listaGrupo($codN)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                if ($this->codGrupo != "") {
                    $select =   "select cod_grupo, nom_grupo
                                from patrimonio.grupo
                                where cod_natureza = '$codN'
                                and cod_grupo = '$this->codGrupo'";
                } else {
                    $select =   "select cod_grupo, nom_grupo
                                    from patrimonio.grupo
                                    where cod_natureza = '$codN'";
                }
                //echo $select."<br>";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $codG = $dbConfig->pegaCampo("cod_grupo");
                    $lista[$codG] = $dbConfig->pegaCampo("nom_grupo");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

            /*** Método que lista as espécies para a geração do relatório ***/
            function listaEspecie($codN, $codG)
            {
                $dbConfig = new dataBaseLegado;
                $dbConfig->abreBd();
                if ($this->codEspecie != "") {
                    $select =   "select cod_especie, nom_especie
                                    from patrimonio.especie
                                    where cod_natureza = '$codN'
                                    and cod_grupo = '$codG'
                                    and cod_especie = '$this->codEspecie'";
                } else {
                    $select =   "select cod_especie, nom_especie
                                    from patrimonio.especie
                                    where cod_natureza = '$codN'
                                    and cod_grupo = '$codG'";
                }
                //echo $select."<br>";
                $dbConfig->abreSelecao($select);
                while (!$dbConfig->eof()) {
                    $codE = $dbConfig->pegaCampo("cod_especie");
                    $lista[$codE] = $dbConfig->pegaCampo("nom_especie");
                    $dbConfig->vaiProximo();
                }
                $dbConfig->limpaSelecao();

                return $lista;
                $dbConfig->fechaBd();
            }

        /*** Método que monta o filtro de uma query de busca em um relatório ***/
        function montaFiltro($natureza = "",$grupo = "",$especie = "",$orgao = "",$unidade = "",$dpto = "",$setor = "",$local = "",$exercicio = "",$codBemInicial ="" ,$codBemFinal ="",$dataInicial="",$dataFinal="",$aliasEspecie="E",$aliasHist="H")
        {
            $filtro  = "";
            if ($natureza) {
                $filtro .= "    AND $aliasEspecie.cod_natureza = '".$natureza."' \n";
            }

            if( $grupo )
                $filtro .= "    AND $aliasEspecie.cod_grupo = '".$grupo."' \n";

            if( $especie )
                $filtro .= "    AND $aliasEspecie.cod_especie = '".$especie."' \n";

            if( $orgao )
                $filtro .= "    AND $aliasHist.cod_orgao = '".$orgao."' \n";

            if( $unidade )
                $filtro .= "    AND $aliasHist.cod_unidade = '".$unidade."' \n";

            if( $dpto )
                $filtro .= "    AND $aliasHist.cod_departamento = '".$dpto."' \n";

            if( $setor )
                $filtro .= "    AND $aliasHist.cod_setor = '".$setor."' \n";

            if( $local )
                $filtro .= "    AND $aliasHist.cod_local = '".$local."' \n";

            if( $exercicio )
                $filtro .= "    AND $aliasHist.ano_exercicio = '".$exercicio."' \n";

            if ($codBemFinal && $codBemInicial) {
                $filtro .= "    AND $aliasHist.cod_bem BETWEEN '".$codBemInicial."' AND '".$codBemFinal."'  \n";
            } elseif ($exercicio) {
                $filtro .= "    AND $aliasHist.cod_bem = '".$codBemFinal."' \n";
            }
            if ($codBemInicial && !$codBemFinal) {
                $filtro .= "    AND $aliasHist.cod_bem      = '".$codBemInicial."' \n";
            }
            if ($dataInicial != '' && $dataFinal != '') {
                $filtro .= "    AND B.dt_aquisicao BETWEEN to_date ('".$dataInicial."','dd/mm/yyyy') AND to_date ('".$dataFinal."','dd/mm/yyyy') \n";
            }

            return $filtro;
        }//Fim da function montaFiltro;

    /*** Método que gera o relatório de Manutenção ***/
        function relatorioManutencao($dataInicial, $dataFinal, $situacao)
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            if ($situacao != "xxx") {
                $select =   "select manut.cod_bem, manut.observacao, manut.dt_agendamento, manut.dt_realizacao,
                            bem.cod_natureza, bem.cod_grupo, bem.cod_especie
                                from patrimonio.manutencao as manut, patrimonio.bem_atributo_especie as bem
                                where manut.cod_bem = bem.cod_bem
                                and dt_agendamento >= '$dataInicial'
                                and dt_agendamento <= '$dataFinal'
                                and lower(observacao) like lower('%$situacao%')";
            } else {
                $select =   "select manut.cod_bem, manut.observacao, manut.dt_agendamento, manut.dt_realizacao,
                            bem.cod_natureza, bem.cod_grupo, bem.cod_especie
                                from patrimonio.manutencao as manut, patrimonio.bem_atributo_especie as bem
                                where manut.cod_bem = bem.cod_bem
                                and dt_agendamento >= '$dataInicial'
                                and dt_agendamento <= '$dataFinal'";
            }
            echo $select;
            $dbConfig->abreSelecao($select);
            $this->sqlPaginacao = $select;
            while (!$dbConfig->eof()) {
                $cod = $dbConfig->pegaCampo("cod_bem");
                $listaObs[$cod] = $dbConfig->pegaCampo("cod_natureza")."/".$dbConfig->pegaCampo("cod_grupo")."/".
                $dbConfig->pegaCampo("cod_especie")."/".$dbConfig->pegaCampo("observacao")."/".$dbConfig->pegaCampo("dt_agendamento")."/".
                $dbConfig->pegaCampo("dt_realizacao");
                $dbConfig->vaiProximo();
            }
            $dbConfig->limpaSelecao();

            return $listaObs;
            return $listaDta;

            return $listaDtr;
            $dbConfig->fechaBd();
        }

    /*** Método que gera o relatório de centro de custo ***/
        function relCentroCusto()
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            /*$select  =      "select orgao.cod_orgao, orgao.nom_orgao,
                            unidade.cod_unidade, unidade.nom_unidade,
                            depart.cod_departamento, depart.nom_departamento,
                            setor.cod_setor, setor.nom_setor,
                            local.cod_local, local.nom_local
                                from administracao.orgao as orgao, administracao.unidade as unidade, administracao.departamento as depart, administracao.setor as
                                setor, administracao.local as local
                                where orgao.cod_orgao = unidade.cod_orgao
                                and orgao.cod_orgao = depart.cod_orgao
                                and depart.cod_unidade = unidade.cod_unidade
                                and setor.cod_orgao = orgao.cod_orgao
                                and setor.cod_unidade = unidade.cod_unidade
                                and setor.cod_departamento = depart.cod_departamento
                                and local.cod_orgao = orgao.cod_orgao
                                and local.cod_unidade = unidade.cod_unidade
                                and local.cod_departamento = depart.cod_departamento
                                and local.cod_setor = setor.cod_setor"; */
            $select = "select distinct orgao.cod_orgao || '.' || unidade.cod_unidade || '.' || depart.cod_departamento || '.' ||
            setor.cod_setor || '.' || local.cod_local as codigo,
            orgao.nom_orgao, unidade.nom_unidade, depart.nom_departamento, setor.nom_setor, local.nom_local
                from administracao.orgao as orgao, administracao.unidade as unidade, administracao.departamento as depart, administracao.setor as setor, administracao.local as local
                where orgao.cod_orgao = unidade.cod_orgao
                and orgao.cod_orgao = depart.cod_orgao
                and orgao.cod_orgao = setor.cod_orgao
                and unidade.cod_unidade = depart.cod_unidade
                and unidade.cod_unidade = setor.cod_unidade
                and depart.cod_departamento = setor.cod_departamento
                and local.cod_setor = setor.cod_setor
                AND orgao.cod_orgao > 0 order by nom_orgao, nom_unidade, nom_departamento, nom_setor";
            $dbConfig->abreSelecao($select);
            //$local = "select cod_local, nom_local from ".LOCAL;
            //$dbConfig->abreSelecao($select2);
            $this->sqlPaginacao = $select;
            while (!$dbConfig->eof()) {
                $local = $dbConfig->pegaCampo("nom_local");
                if ($local == "")
                    $local = "x";
                $lista[] = $dbConfig->pegaCampo("codigo")."/".$dbConfig->pegaCampo("nom_orgao")."/".$dbConfig->pegaCampo("nom_unidade")."/".
        $dbConfig->pegaCampo("nom_departamento")."/".$dbConfig->pegaCampo("nom_setor")."/".$local;
        $dbConfig->vaiProximo();
            }
            $dbConfig->limpaSelecao();
            //return $cod;
            return $lista;
            $dbConfig->fechaBd();
        }

    /*** Método que gera relatório de classificação ***/
        function relClassificacao()
        {
            $dbConfig = new dataBaseLegado;
            $dbConfig->abreBd();
            $select =   "select natu.cod_natureza, grupo.cod_grupo, especie.cod_especie, natu.nom_natureza, grupo.nom_grupo,
                        especie.nom_especie
                            from patrimonio.natureza as natu, patrimonio.grupo as grupo, patrimonio.especie as especie
                            where grupo.cod_natureza = natu.cod_natureza
                            and especie.cod_natureza = natu.cod_natureza
                            and especie.cod_grupo = grupo.cod_grupo order by especie.cod_natureza, cod_grupo, cod_especie";
            $dbConfig->abreSelecao($select);
            $this->sqlPaginacao = $select;
            while (!$dbConfig->eof()) {
                $lista[] = $dbConfig->pegaCampo("cod_natureza")."/".$dbConfig->pegaCampo("cod_grupo")."/".
                $dbConfig->pegaCampo("cod_especie")."/".$dbConfig->pegaCampo("nom_natureza")."/".$dbConfig->pegaCampo("nom_grupo")."/".
                $dbConfig->pegaCampo("nom_especie");
                $dbConfig->vaiProximo();
            }
            $dbConfig->limpaSelecao();

            return $lista;
            $dbConfig->fechaBd();
        }
}

?>
