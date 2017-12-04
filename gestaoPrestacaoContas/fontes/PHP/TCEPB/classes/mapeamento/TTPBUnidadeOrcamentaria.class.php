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
    * Extensão da Classe de mapeamento
    * Data de Criação: 02/03/2007

    * @author Desenvolvedor: Diogo Zarpelon
    
    $Id: TTPBUnidadeOrcamentaria.class.php 59612 2014-09-02 12:00:51Z gelson $

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBUnidadeOrcamentaria extends Persistente
{
function recuperaRelacionamentoUnidadeOrcamentaria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoUnidadeOrcamentaria().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoUnidadeOrcamentaria()
{
    $stQuebra = "\n";
    $stSql .= " SELECT                                            ".$stQuebra;
    $stSql .= "     UO.*,                                         ".$stQuebra;
    $stSql .= "     OO.nom_orgao,                                 ".$stQuebra;
    $stSql .= "     OO.exercicio as exercicio_orgao,              ".$stQuebra;
    $stSql .= "     UO.exercicio as exercicio_unidade,            ".$stQuebra;
    $stSql .= "     PF.cpf,                                       ".$stQuebra;
    $stSql .= "     CGM.nom_cgm,                                  ".$stQuebra;
    $stSql .= "     tu.natureza_juridica                          ".$stQuebra;
    $stSql .= " FROM                                              ".$stQuebra;
    $stSql .= "     orcamento.unidade       as UO,                ".$stQuebra;
    $stSql .= "     orcamento.orgao         as OO,                ".$stQuebra;
    $stSql .= "     sw_cgm                  as CGM,               ".$stQuebra;
    $stSql .= "     sw_cgm_pessoa_fisica    as PF,                ".$stQuebra;
    $stSql .= "     tcepb.uniorcam          as tu                 ".$stQuebra;
    $stSql .= " WHERE                                             ".$stQuebra;
    $stSql .= "    UO.exercicio          = OO.exercicio      AND  ".$stQuebra;
    $stSql .= "    UO.num_orgao          = OO.num_orgao      AND  ".$stQuebra;
    $stSql .= "                                                   ".$stQuebra;
    $stSql .= "    OO.usuario_responsavel = CGM.numcgm       AND  ".$stQuebra;
    $stSql .= "    CGM.numcgm            = PF.numcgm         AND  ".$stQuebra;
    $stSql .= "                                                   ".$stQuebra;
    $stSql .= "    tu.num_orgao          = UO.num_orgao      AND  ".$stQuebra;
    $stSql .= "    tu.num_unidade        = UO.num_unidade         ".$stQuebra;

    return $stSql;
}

}
