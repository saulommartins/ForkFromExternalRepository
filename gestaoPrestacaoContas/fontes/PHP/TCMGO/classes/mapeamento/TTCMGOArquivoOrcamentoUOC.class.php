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
    * Data de Criação: 26/01/2015

    * @author Analista: Ane Caroline
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOArquivoOrcamentoUOC extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMGOArquivoOrcamentoUOC()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

public function recuperaUnidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaUnidade",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaUnidade()
{
    $stSql  = "SELECT '10'::VARCHAR AS tipo_registro,
                      unidade_responsavel.num_orgao AS cod_orgao,
                      unidade_responsavel.num_unidade AS cod_unidade,
                      unidade.nom_unidade AS desc_unidade,
                      unidade_responsavel.exercicio,
                      orgao.cod_tipo AS tipo_unidade,        
                      '0' AS numero_registro                
                 FROM tcmgo.unidade_responsavel
           INNER JOIN orcamento.unidade
                   ON unidade.num_orgao   = unidade_responsavel.num_orgao
                  AND unidade.num_unidade = unidade_responsavel.num_unidade
                  AND unidade.exercicio   = unidade_responsavel.exercicio
           INNER JOIN tcmgo.orgao
                   ON unidade.num_orgao   = unidade_responsavel.num_orgao
                  AND unidade.exercicio   = unidade_responsavel.exercicio
                WHERE unidade_responsavel.exercicio = '".$this->getDado('exercicio')."'
             GROUP BY unidade_responsavel.num_orgao,
                      unidade_responsavel.num_unidade,
                      unidade_responsavel.exercicio,
                      unidade.nom_unidade,
                      orgao.cod_tipo      ";

    return $stSql;
}
}
