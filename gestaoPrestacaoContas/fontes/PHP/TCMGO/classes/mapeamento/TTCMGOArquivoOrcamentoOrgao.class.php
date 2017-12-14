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
    * Data de Criação: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOIde.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOArquivoOrcamentoOrgao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCMGOArquivoOrcamentoOrgao()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

public function recuperaOrgao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaOrgao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaOrgao()
{
    $stSql  = "
       SELECT '10' AS tipo_registro
            , tcmgo.orgao.num_orgao
            , cgm_gestor_fisica.cpf AS cpf_gestor
            , cod_tipo AS tipo_orgao
            , 0 AS numero_sequencial
         FROM orcamento.orgao
   INNER JOIN tcmgo.orgao
           ON tcmgo.orgao.num_orgao = orcamento.orgao.num_orgao
          AND tcmgo.orgao.exercicio = orcamento.orgao.exercicio
   INNER JOIN tcmgo.orgao_gestor
           ON tcmgo.orgao_gestor.exercicio = tcmgo.orgao.exercicio
          AND tcmgo.orgao_gestor.num_orgao = tcmgo.orgao.num_orgao
   INNER JOIN sw_cgm AS cgm_gestor
           ON cgm_gestor.numcgm = tcmgo.orgao_gestor.numcgm
   INNER JOIN sw_cgm_pessoa_fisica AS cgm_gestor_fisica
           ON cgm_gestor_fisica.numcgm = cgm_gestor.numcgm
        WHERE tcmgo.orgao.exercicio = '".$this->getDado('exercicio')."'
    ";

    return $stSql;
}
}
