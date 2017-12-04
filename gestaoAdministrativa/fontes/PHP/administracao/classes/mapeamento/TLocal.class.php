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
* Classe de mapeamento para administracao.orgao
* Data de Criação: 06/04/2006

* @author Analista: Cassiano
* @author Desenvolvedor: Fernando Zank Correa Evangelista

$Id: TLocal.class.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-01.03.97
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
    * Efetua conexão com tabela de Órgão
    * Data de Criação   : 24/03/2004
    * @author Fernando Zank Correa Evangelista
*/
class TLocal extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TLocal()
    {
        parent::Persistente();
        $this->setTabela('administracao.local');
        $this->setCampoCod('cod_local');

        $this->AddCampo('cod_local','integer',true,'',true,false);
        $this->AddCampo('cod_departamento','integer',true,'',true,true);
        $this->AddCampo('cod_orgao','integer',true,'',true,true);
        $this->AddCampo('ano_exercicio','char',true,'4,true,true');
        $this->AddCampo('cod_unidade','integer',true,'',true,true);
        $this->AddCampo('cod_setor','integer',true,'',true,true);
        $this->AddCampo('usuario_responsavel','integer',true,'',false,true);
        $this->AddCampo('nom_local','varchar',true,'60',false,false);

    }

    public function recuperaLocalizacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaLocalizacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaLocalizacao()
    {
        $stSql = "
            SELECT orgao.cod_orgao
                 , orgao.nom_orgao
                 , unidade.cod_unidade
                 , unidade.nom_unidade
                 , departamento.cod_departamento
                 , departamento.nom_departamento
                 , setor.cod_setor
                 , setor.nom_setor
                 , local.cod_local
                 , local.nom_local
                 , local.ano_exercicio
              FROM administracao.local
        INNER JOIN administracao.orgao
                ON orgao.cod_orgao = local.cod_orgao
               AND orgao.ano_exercicio = local.ano_exercicio
        INNER JOIN administracao.unidade
                ON unidade.cod_orgao = local.cod_orgao
               AND unidade.cod_unidade = local.cod_unidade
               AND unidade.ano_exercicio = local.ano_exercicio
        INNER JOIN administracao.departamento
                ON departamento.cod_orgao = local.cod_orgao
               AND departamento.cod_unidade = local.cod_unidade
               AND departamento.cod_departamento = local.cod_departamento
               AND departamento.ano_exercicio = local.ano_exercicio
        INNER JOIN administracao.setor
                ON setor.cod_orgao = local.cod_orgao
               AND setor.cod_unidade = local.cod_unidade
               AND setor.cod_departamento = local.cod_departamento
               AND setor.cod_setor = local.cod_setor
               AND setor.ano_exercicio = local.ano_exercicio
             WHERE local.cod_orgao = ".$this->getDado('cod_orgao')."
               AND local.cod_unidade = ".$this->getDado('cod_unidade')."
               AND local.cod_departamento = ".$this->getDado('cod_departamento')."
               AND local.cod_setor = ".$this->getDado('cod_setor')."
               AND local.cod_local = ".$this->getDado('cod_local')."
               AND local.ano_exercicio = '".$this->getDado('ano_exercicio')."'
        ";

        return $stSql;
    }

}
