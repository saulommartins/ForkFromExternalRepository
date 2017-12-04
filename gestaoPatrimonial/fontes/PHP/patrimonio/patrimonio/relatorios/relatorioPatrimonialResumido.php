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
    * Relatório Patrimonial Resumido
    * Data de Criação   : 02/04/2003

    * @author Desenvolvedor  Ricardo Lopes de Alencar

    * @ignore

    $Revision: 18283 $
    $Name$
    $Autor: $
    $Date: 2006-11-28 13:48:08 -0200 (Ter, 28 Nov 2006) $

    * Casos de uso: uc-03.01.09
*/

/*
$Log$
Revision 1.14  2006/11/28 15:48:08  larocca
Bug #6936

Revision 1.13  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.12  2006/07/06 12:11:28  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/botoesPdfLegado.class.php';
include_once '../relatorio.class.php';

//Pega os dados gravados na sessão em forma de vetor e gera as variáveis locais
$arFiltro = Sessao::read('filtro');
if ( is_array($arFiltro) ) {
    foreach ($arFiltro as $chave=>$valor) {
        $$chave = $valor;
    }
}

//Gera o filtro de acordo com os parâmetros fornecidos pelo cliente
//if ( !isset($filtro) ) {

    $arLocal = preg_split( "/[^a-zA-Z0-9]/", $_POST['codMasSetor'] );
    if ($_POST['inCodBemInicial']) {
        $codBemInicial = $_POST['inCodBemInicial'];
    }
    if ($_POST['inCodBemFinal']) {
        $codBemFinal = $_POST['inCodBemFinal'];
    }

    if ($_POST['codNatureza']       AND $_POST['codNatureza'] != 'xxx') {
        $natureza = $_POST['codNatureza'];
    }
    if ($_POST['codGrupo']          AND $_POST['codGrupo'] != 'xxx') {
        $grupo = $_POST['codGrupo'];
    }
    if ($_POST['codEspecie']        AND $_POST['codEspecie'] != 'xxx') {
        $especie = $_POST['codEspecie'];
    }
    if ($arLocal[0] > 0) {
        $orgao = $arLocal[0];
    }
    if ($arLocal[1] > 0) {
        $unidade = $arLocal[1];
    }
    if ($arLocal[2] > 0) {
        $departamento = $arLocal[2];
    }
    if ($arLocal[3] > 0) {
        $setor = $arLocal[3];
    }
    if ($arLocal[4] > 0) {
        $local = $arLocal[4];
    }
    if ($arLocal[5] > 0) {
        $exercicio = $arLocal[5];
    }
    $relatorio = new relatorio;
    $filtro    = $relatorio->montaFiltro( $natureza, $grupo, $especie, $orgao, $unidade, $departamento, $setor, $local, $exercicioLocal, $codBemInicial, $codBemFinal,$dataInicial,$dataFinal);
    // echo "FILTRO = ".$filtro."<br>";
//}

//Monta a query padrão sem o filtro
/*$sql = "SELECT DISTINCT
            B.cod_bem,
            B.num_placa,
            B.descricao,
            E.nom_especie,
            L.nom_local
        FROM
            patrimonio.bem                  as B,
            patrimonio.vw_bem_ativo       as BA,
            patrimonio.historico_bem        as H,
            patrimonio.vw_ultimo_historico  as U,
            patrimonio.especie              as E,
            administracao.local                as L
        WHERE
            BA.cod_bem         = B.cod_bem
        AND BA.cod_bem         = U.cod_bem
        AND BA.cod_bem         = H.cod_bem
        AND H.timestamp        = U.timestamp
        AND B.cod_natureza     = E.cod_natureza
        AND B.cod_grupo        = E.cod_grupo
        AND B.cod_especie      = E.cod_especie
        AND H.cod_local        = L.cod_local
        AND H.cod_setor        = L.cod_setor
        AND H.cod_departamento = L.cod_departamento
        AND H.cod_unidade      = L.cod_unidade
        AND H.cod_orgao        = L.cod_orgao \n".$filtro;
*/
$sql = "
SELECT
       B.cod_bem
     , B.num_placa
     , B.descricao
     , E.nom_especie
     , L.nom_local
  FROM
       patrimonio.bem                           AS B
       INNER JOIN patrimonio.historico_bem      AS H
               ON H.cod_bem          = B.cod_bem
              AND H.timestamp        = ( SELECT
                                                timestamp
                                           FROM
                                                patrimonio.historico_bem AS HB
                                          WHERE
                                                HB.cod_bem = B.cod_bem
                                          ORDER BY timestamp DESC limit 1
                                        )
       INNER JOIN patrimonio.especie            AS E
               ON E.cod_especie      = B.cod_especie
              AND E.cod_grupo        = B.cod_grupo
              AND E.cod_natureza     = B.cod_natureza
       INNER JOIN administracao.local           AS L
               ON L.cod_local        = H.cod_local
              AND L.cod_setor        = H.cod_setor
              AND L.cod_departamento = H.cod_departamento
              AND L.cod_unidade      = H.cod_unidade
              AND L.cod_orgao        = H.cod_orgao
        LEFT JOIN patrimonio.bem_baixado        AS BB
               ON BB.cod_bem         = B.cod_bem
  WHERE
        BB.cod_bem IS NULL \n".$filtro;
$ordenar = "B.cod_bem";

//Mostra a opção de imprimir ou salvar o relatório
$sqlPDF     = $sql." ORDER BY ".$ordenar." ASC ";
$sXML       = '../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/relatorioPatrimonialResumido.xml';
$botoesPDF  = new botoesPdfLegado;
$botoesPDF->imprimeBotoes($sXML,$sqlPDF,'','');

//Setar variáveis que devem permanecer em todas as páginas
$transf[filtro]  = $filtro;
$transf[ordenar] = $ordenar;
Sessao::write('filtro',$transf);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
