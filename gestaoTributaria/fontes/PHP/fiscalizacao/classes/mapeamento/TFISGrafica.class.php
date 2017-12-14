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
/*
    * Classe de regra de mapeamento para FISCALIZACAO.GRAFICA
    * Data de Criacao: 26/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: TFISGrafica.class.php 29237 2008-04-16 12:02:48Z fabio $

    *Casos de uso: uc-05.07.03

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TFISGrafica extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
function TFISGrafica()
{
    parent::Persistente();
    $this->setTabela( 'fiscalizacao.grafica' );

    $this->setCampoCod( 'numcgm' );
    $this->setComplementoChave( '' );

    $this->AddCampo( 'numcgm','integer',true,'',true,false );
    $this->AddCampo( 'ativo','boolean',true,'',false,false );
}

function recuperaListaGrafica(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaListaGrafica().$stCondicao.$stOrdem;
    //$this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaGrafica()
{
    $stSql =" SELECT grafica.numcgm,                 \n";
    $stSql.="   CASE grafica.ativo                   \n";
    $stSql.="        WHEN true  THEN 'Ativo'         \n";
    $stSql.="        WHEN false THEN 'Inativo'       \n";
    $stSql.="    END AS ativo                        \n";
    $stSql.="       ,sw_cgm.nom_cgm                  \n";
    $stSql.="   FROM fiscalizacao.grafica            \n";
    $stSql.="       ,sw_cgm                          \n";
    $stSql.="  WHERE grafica.numcgm = sw_cgm.numcgm  \n";

    return $stSql;
}

}
