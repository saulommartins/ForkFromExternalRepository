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
    * Classe de mapeamento da tabela PPA.PPA_RECEITA_RECURSO
    * Data de Criação: 25/06/2007

    * @author Analista:
    * @author Desenvolvedor: Leandro Zis

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.09.05
*/

/*
$Log$
Revision 1.1  2007/06/26 17:01:50  leandro.zis
uc 02.09.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPPAPPAReceitaRecurso extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPPAPPAReceitaRecurso()
    {
        parent::Persistente();
        $this->setTabela('ppa.ppa_receita_recurso');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_configuracao, ano, cod_recurso, exercicio, cod_receita');

        $this->AddCampo('cod_configuracao', 'integer', true, '', true, 'TPPAPPAReceita');
        $this->AddCampo('cod_receita', 'integer', true, '', true, 'TPPAPPAReceita');
        $this->AddCampo('exercicio', 'char', true, '4', true, 'TPPAPPAReceita');
        $this->AddCampo('cod_recurso', 'integer', true, '', true, 'TOrcamentoRecurso');
        $this->AddCampo('ano', 'char', true, '1', true, false);
        $this->AddCampo('valor', 'numeric', true, '14,2', false, false);

    }

    public function recuperaRegioes(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegioes",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaRegioes()
    {
        $stSql = "            select regiao.cod_regiao                                ";
        $stSql.= "                  ,regiao.codigo                                    ";
        $stSql.= "                  ,regiao.nome                                      ";
        $stSql.= "              from ppa.regiao                                       ";

        return $stSql;
    }

} // end of class
