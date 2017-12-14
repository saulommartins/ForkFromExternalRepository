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
  * Classe da Função Numeracao Divida
  * Data de criação : 31/01/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @package URBEM
  * @subpackage Funcao

    * $Id: FNumeracaoDivida.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.02
**/

/*
$Log$
Revision 1.2  2007/07/02 20:47:00  cercato
passando parametro com aspas pq eh var char.

Revision 1.1  2007/02/26 17:19:55  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include_once( CLA_PERSISTENTE );

class FNumeracaoDivida extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FNumeracaoDivida()
{
    parent::Persistente();
    $this->AddCampo('valor','varchar'  ,false       ,''     ,false   ,false );
}

function executaFuncao(&$rsRecordset, $stParametros, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql  = $this->montaExecutaFuncao($stParametros);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordset, $stSql, $boTransacao );

    return $obErro;
}

function montaExecutaFuncao($stParametros)
{
    $stSql  = " SELECT  numeracaodivida ('".$stParametros."') as valor \r\n";

    return $stSql;
}

}
