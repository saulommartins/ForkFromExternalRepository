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
    * Data de Criação: 11/07/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Anderson C. Konze

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 25612 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-24 17:03:30 -0300 (Seg, 24 Set 2007) $

    * Casos de uso: uc-02.08.15

*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTRNProcessoFundamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTRNProcessoFundamento()
{
    parent::Persistente();
    $this->setTabela('tcern.processo_fundamento');
    $this->setComplementoChave('cod_licitacao, cod_modalidade, cod_entidade, exercicio');

    $this->AddCampo('exercicio'         ,'varchar',true,'4',true,false);
    $this->AddCampo('cod_entidade'      ,'integer',true,'' ,true,false);
    $this->AddCampo('cod_modalidade'    ,'integer',true,'' ,true,false);
    $this->AddCampo('cod_licitacao'     ,'integer',true,'' ,true,false);
    $this->AddCampo('fundamento_legal'  ,'varchar',true,'4',false,false);

    $this->SetDado("exercicio",Sessao::getExercicio());
}

function montaRecuperaRelacionamento()
{
    $stSql = "
    SELECT   lici.*, cgm.nom_cgm, profu.fundamento_legal, moda.descricao as ds_modalidade
    FROM     orcamento.entidade         as enti
            ,sw_cgm                     as cgm
            ,compras.modalidade         as moda
            ,licitacao.licitacao        as lici
    LEFT JOIN
            tcern.processo_fundamento  as profu
        ON (
                lici.exercicio      = profu.exercicio
        AND     lici.cod_entidade   = profu.cod_entidade
        AND     lici.cod_modalidade = profu.cod_modalidade
        AND     lici.cod_licitacao  = profu.cod_licitacao
        )
    WHERE   lici.exercicio = '".$this->getDado('exercicio')."'
    AND     lici.exercicio      = enti.exercicio
    AND     lici.cod_entidade   = enti.cod_entidade
    AND     enti.numcgm         = cgm.numcgm
    AND     lici.cod_modalidade = moda.cod_modalidade
    ORDER BY lici.cod_entidade, lici.cod_modalidade, lici.cod_licitacao
    ";

    return $stSql;
}

}
