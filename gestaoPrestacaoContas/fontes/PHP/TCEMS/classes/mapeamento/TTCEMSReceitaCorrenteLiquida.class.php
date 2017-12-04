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
 * Mapeamento da tabela tcems.receita_corrente_liquida
 * @author      Desenvolvedor   Davi Ritter Aroldi
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TTCEMSReceitaCorrenteLiquida extends Persistente
{
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('tcems.receita_corrente_liquida');
        $this->setCampoCod        ('');
        $this->setComplementoChave('mes, ano, exercicio');

        $this->AddCampo('mes'          , 'integer', true, ''    , true , false);
        $this->AddCampo('ano'          , 'varchar', true, '4'   , true , false);
        $this->AddCampo('exercicio'    , 'varchar', true, '4'   , true , true );
        $this->AddCampo('valor'        , 'numeric', true, '14,2', false, false);
    }

    public function recuperaValorQuadrimestre1(&$rsRecordSet)
    {
        $stSql = "
            SELECT sum(valor) as vl_quadrimestre
              FROM tcems.receita_corrente_liquida
             WHERE exercicio = '".Sessao::read('exercicio')."'
               AND mes in (1,2,3,4)
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet);
    }

    public function recuperaValorQuadrimestre2(&$rsRecordSet)
    {
        $stSql = "
            SELECT sum(valor) as vl_quadrimestre
              FROM tcems.receita_corrente_liquida
             WHERE exercicio = '".Sessao::read('exercicio')."'
               AND mes in (5,6,7,8)
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet);
    }

    public function recuperaValorQuadrimestre3(&$rsRecordSet)
    {
        $stSql = "
            SELECT sum(valor) as vl_quadrimestre
              FROM tcems.receita_corrente_liquida
             WHERE exercicio = '".Sessao::read('exercicio')."'
               AND mes in (9,10,11,12)
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet);
    }
}
