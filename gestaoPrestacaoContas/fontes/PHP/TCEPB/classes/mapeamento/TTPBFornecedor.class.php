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
    * Classe de mapeamento da tabela
    * Data de Criação: 06/03/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Autor:$
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
    
    $Id: TTPBFornecedor.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.03.00

*/

/*
$Log$
Revision 1.5  2007/05/11 20:05:03  tonismar
corrigido join com tabela sw_cgm

Revision 1.4  2007/04/28 01:59:05  diego
correções de sql

Revision 1.3  2007/04/23 15:25:13  rodrigo_sr
uc-06.03.00

Revision 1.2  2007/03/14 00:29:38  cleisson
ajustes

Revision 1.1  2007/03/07 00:13:03  diego
Primeira versão...

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GP_COM_MAPEAMENTO."TComprasFornecedor.class.php");

class TTPBFornecedor extends TComprasFornecedor
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBFornecedor()
{
    parent::TComprasFornecedor();
    $this->setDado('exercicio',Sessao::getExercicio());
}

function montaRecuperaTodos()
{
/*
    $stSql  =" SELECT  case when  pf.numcgm is not null   \n";
    $stSql .="                  then pf.cpf   \n";
    $stSql .="               else    pj.cnpj   \n";
    $stSql .="         end as numero   \n";
    $stSql .="         ,case when  pf.numcgm is not null   \n";
    $stSql .="                  then 1   \n";
    $stSql .="               else    2   \n";
    $stSql .="         end as cpf_cnpj   \n";
    $stSql .="         ,cg.nom_cgm   \n";
    $stSql .=" FROM     compras.fornecedor as fo   \n";
    $stSql .="         ,sw_cgm             as cg   \n";
    $stSql .="     LEFT JOIN   sw_cgm_pessoa_fisica as pf   \n";
    $stSql .="     ON ( cg.numcgm = pf.numcgm )   \n";
    $stSql .="     LEFT JOIN   sw_cgm_pessoa_juridica as pj   \n";
    $stSql .="     ON ( cg.numcgm = pj.numcgm )   \n";
    $stSql .="    \n";
    $stSql .=" WHERE   fo.cgm_fornecedor = cg.numcgm   \n";
    $stSql .=" AND     fo.ativo = true                 \n";
    $stSql .=" AND     fo.exercicio = '".$this->getDado("stExercicio"). "' \n";
*/

    $stSql .=" SELECT  case when  pf.cpf is not null   then pf.cpf  \n";
    $stSql .="              when pj.cnpj is not null then pj.cnpj   \n";
    $stSql .="            else '123456789'   \n";
    $stSql .="         end as numero   \n";
    $stSql .="         ,case when  pf.numcgm is not null   \n";
    $stSql .="                  then 1   \n";
    $stSql .="               else    2   \n";
    $stSql .="         end as cpf_cnpj   \n";
    $stSql .="         ,cg.nom_cgm   \n";
    $stSql .=" FROM    (   \n";
    $stSql .="             SELECT   cg.numcgm   \n";
    $stSql .="                     ,cg.nom_cgm   \n";
    $stSql .="             FROM    sw_cgm   as cg   \n";
    $stSql .="                    ,empenho.pre_empenho as pre   \n";
    $stSql .="             WHERE   cg.numcgm = pre.cgm_beneficiario and pre.exercicio = '".$this->getDado('exercicio')."'   \n";
    $stSql .="             GROUP BY cg.numcgm, cg.nom_cgm   \n";
    $stSql .="         ) as cg   \n";
    $stSql .="         LEFT JOIN   sw_cgm_pessoa_fisica as pf   \n";
    $stSql .="         ON ( cg.numcgm = pf.numcgm )   \n";
    $stSql .="         LEFT JOIN   sw_cgm_pessoa_juridica as pj   \n";
    $stSql .="         ON ( cg.numcgm = pj.numcgm )   \n";
    $stSql .=" WHERE  ( pf.cpf is not null ) OR ( pj.cnpj is not null )   \n";
    $stSql .=" ORDER BY cg.nom_cgm                                        \n";

    return $stSql;
}

}

?>