<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos (urbem@cnm.org.br)      *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo  sob *
    * os termos da Licença Pública Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a versão 2 da Licença, como (a seu critério) qualquer versão *
    * posterior.                                                                     *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral  do  GNU  junto  com *
    * este programa; se não, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de mapeamento da tabela FN_ORCAMENTO_GERAR_ORCAMENTO
    * Data de Criação: 26/07/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso : uc-02.01.31
*/

/*
$Log$
Revision 1.7  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FOrcamentoGerarOrcamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FOrcamentoGerarOrcamento()
{
    parent::Persistente();

    $this->setTabela('orcamento.fn_gerar_orcamento');

    $this->AddCampo('stExercicio'     ,'varchar',false,''    ,false,false);
    $this->AddCampo('stParametros'    ,'varchar',false,''    ,false,false);
    $this->AddCampo('stFiltro'        ,'varchar',false,''    ,false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT *                                                     
                    FROM ".$this->getTabela()."('".$this->getDado("stExercicio")."'
                                              ,'" .$this->getDado("stParametros"). "'
                                              ,'" .$this->getDado("stFiltro")."'
                ) as stRetorno
            ";

    return $stSql;
}

}