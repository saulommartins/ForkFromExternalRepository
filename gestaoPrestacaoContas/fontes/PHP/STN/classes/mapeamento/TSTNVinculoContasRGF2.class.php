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
 * Mapeamento da tabela stn.vinculo_contas_rgf_2
 *
 * @category    Urbem
 * @package     STN
 * @author      Desenvolvedor   Eduardo Paculski Schitz
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TSTNVinculoContasRGF2 extends Persistente
{
    /**
     * Método Construtor da classe TSTNContaDedutoraTributos
     *
     * @author      Desenvolvedor   Eduardo Paculski Schitz
     *
     * @return void
     */
    public function TSTNVinculoContasRGF2()
    {
        parent::Persistente();

        $this->setTabela          ('stn.vinculo_contas_rgf_2');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_conta,cod_plano,exercicio');

        $this->AddCampo('cod_conta', 'integer'  , true, ''    , true , true);
        $this->AddCampo('cod_plano', 'integer'  , true, ''    , true , true);
        $this->AddCampo('exercicio', 'varchar'  , true, '4'   , true , true);
    }

    /**
     * Método que retorna os vínculos das contas configuráveis do RGF 2
     *
     * @author      Desenvolvedor   Eduardo Paculski Schitz
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarVinculoContasRGF2(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $stSql = "
            SELECT vinculo_contas_rgf_2.cod_conta
                 , plano_analitica.cod_plano
                 , plano_conta.cod_estrutural
                 , plano_conta.nom_conta
                 , plano_conta.exercicio
              FROM stn.vinculo_contas_rgf_2
        INNER JOIN contabilidade.plano_analitica
                ON plano_analitica.cod_plano = vinculo_contas_rgf_2.cod_plano
               AND plano_analitica.exercicio = vinculo_contas_rgf_2.exercicio
        INNER JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
}
