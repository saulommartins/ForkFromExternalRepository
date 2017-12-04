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
    * Classe de regra de negócio para MONETARIO.CREDITO_NORMA
    * Data de Criação: 20/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @package URBEM
    * @subpackage Regra

    * $Id: TMONCreditoNorma.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.10
*/

/*
$Log$
Revision 1.7  2006/09/15 14:46:11  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*include_once    ("../../../includes/Constante.inc.php");*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TMONCreditoNorma extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TMONCreditoNorma()
{
    parent::Persistente();
    $this->setTabela('monetario.credito_norma');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_credito,cod_natureza,cod_genero,cod_especie,cod_norma');

    $this->AddCampo('cod_credito','INTEGER',true,'',true,false);
    $this->AddCampo('cod_natureza','INTEGER',true,'',true,true);
    $this->AddCampo('cod_genero','INTEGER',true,'',true,true);
    $this->AddCampo('cod_especie','INTEGER',true,'',true,true);
    $this->AddCampo('cod_norma','INTEGER',true,'',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('dt_inicio_vigencia','date',true,'',false,false);
}

function recuperaRelacionamentoBuscaNorma(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoBuscaNorma().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoBuscaNorma()
{
    $stSql  = " SELECT                                                                                                                           \n";
    $stSql .= "     *                                                                                                                                 \n";
    $stSql .= " FROM                                                                                                                              \n";
    $stSql .= "     monetario.credito_norma as cm                                                                                 \n";
    $stSql .= "     INNER JOIN normas.norma as no ON no.cod_norma = cm.cod_norma                         \n";
    $stSql .= "     INNER JOIN normas.tipo_norma as tn ON tn.cod_tipo_norma = no.cod_tipo_norma    \n";

return $stSql;

}

} // fecha classe
