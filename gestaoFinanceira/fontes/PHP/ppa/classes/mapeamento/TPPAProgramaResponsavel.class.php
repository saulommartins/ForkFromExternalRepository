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
    * Classe de mapeamento da tabela PPA.PROGRAMA_RESPONSAVEL
    * Data de Criação: 03/10/2008

    * @author Analista: Bruno Ferreira
    * @author Desenvolvedor: Jânio Eduardo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAProgramaResponsavel extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPPAProgramaResponsavel()
    {
        parent::Persistente();

        $this->setTabela('ppa.programa_responsavel');

        $this->setCampoCod('cod_programa');
        $this->setComplementoChave('cod_contrato');

        $this->AddCampo('cod_programa', 'integer', true, '', true, false);
        $this->AddCampo('timestamp_programa_dados', 'timestamp', true, '', false, false);
        $this->AddCampo('cod_contrato', 'integer', true, '', false, true);

    }

    public function recuperaNomCgm(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaNomCgm", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaNomCgm()
    {
        $stSql = "    SELECT DISTINCT p.cod_programa                                            \n";
        $stSql.= "         , p.cod_ppa                                                          \n";
        $stSql.= "         , p.num_programa                                                     \n";
        $stSql.= "         , pr.cod_contrato                                                    \n";
        $stSql.= "         , cgm.nom_cgm                                                        \n";
        $stSql.= "      FROM ppa.programa as p                                                  \n";
        $stSql.= "INNER JOIN ppa.programa_dados as pd                                           \n";
        $stSql.= "        ON p.ultimo_timestamp_programa_dados = pd.timestamp_programa_dados    \n";
        $stSql.= "INNER JOIN ppa.programa_responsavel as pr                                     \n";
        $stSql.= "        ON p.ultimo_timestamp_programa_dados = pr.timestamp_programa_dados    \n";
        $stSql.= "INNER JOIN ppa.programa_orgao_responsavel as por                              \n";
        $stSql.= "        ON p.ultimo_timestamp_programa_dados = por.timestamp_programa_dados   \n";
        $stSql.= " LEFT JOIN ppa.programa_temporario_vigencia as ptv                            \n";
        $stSql.= "        ON p.ultimo_timestamp_programa_dados = ptv.timestamp_programa_dados   \n";
        $stSql.= "INNER JOIN pessoal.contrato as c                                              \n";
        $stSql.= "        ON pr.cod_contrato = c.cod_contrato                                   \n";
        $stSql.= "INNER JOIN pessoal.contrato_servidor cs                                       \n";
        $stSql.= "        ON c.cod_contrato = cs.cod_contrato                                   \n";
        $stSql.= "INNER JOIN pessoal.servidor_contrato_servidor scs                             \n";
        $stSql.= "        ON cs.cod_contrato = scs.cod_contrato                                 \n";
        $stSql.= "INNER JOIN pessoal.servidor s                                                 \n";
        $stSql.= "        ON scs.cod_servidor = s.cod_servidor                                  \n";
        $stSql.= "INNER JOIN sw_cgm as cgm                                                      \n";
        $stSql.= "        ON s.numcgm = cgm.numcgm                                              \n";

        return $stSql;
    }

}
