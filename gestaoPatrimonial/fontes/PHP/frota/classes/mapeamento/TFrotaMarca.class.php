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
  * Mapeamento da tabela frota.veiculo
  * Data de criação : 15/03/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Id: TFrotaMarca.class.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-03.02.10
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaMarca extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

function TFrotaMarca()
{
    parent::Persistente();
    $this->setTabela('frota.marca');
    $this->setCampoCod('cod_marca');
    $this->setComplementoChave('');
    $this->AddCampo('cod_marca','integer',true,'',true,false);
    $this->AddCampo('nom_marca','varchar',true,'"30"',false,false);
}

function recuperaMarca(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMarca().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMarca()
{
    $stSql  = "select                       \n";
    $stSql .= "    *                        \n";
    $stSql .= "from                         \n";
    $stSql .= "    frota.marca              \n";
    if ($this->getDado('inCodMarca'))
        $stSql .= "    where cod_marca = ".$this->getDado('inCodMarca')."             \n";
    $stSql .= "   order by             \n";

    return $stSql;

}

}
