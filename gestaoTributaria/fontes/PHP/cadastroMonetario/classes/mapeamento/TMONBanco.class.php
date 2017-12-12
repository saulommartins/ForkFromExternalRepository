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
    * Classe de regra de negócio para MONETARIO.CREDITO
    * Data de Criação: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONBanco.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.01
*/

/*
$Log$
Revision 1.10  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONBanco extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONBanco()
{
    parent::Persistente();
    $this->setTabela("monetario.banco");

    $this->setCampoCod('cod_banco');
    $this->setComplementoChave('');

    $this->AddCampo('cod_banco','integer',true,'',true,false);
    $this->AddCampo('num_banco','varchar',true,'10',false,false);
    $this->AddCampo('nom_banco','varchar',true,'80',false,false);

}

function montaRecuperaBancos()
{
    $stSql = "SELECT * FROM monetario.banco ";

    return $stSql;
}

function recuperaBancos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY num_banco ASC";
    $stSql  = $this->montaRecuperaBancos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
