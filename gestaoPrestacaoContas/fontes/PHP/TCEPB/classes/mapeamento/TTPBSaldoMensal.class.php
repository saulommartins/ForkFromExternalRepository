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
    * Data de Criação: 13/03/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.5  2007/05/11 20:23:14  hboaventura
Arquivos para geração do TCEPB

Revision 1.4  2007/05/10 21:43:51  hboaventura
Arquivos para geração do TCEPB

Revision 1.3  2007/04/23 15:30:37  rodrigo_sr
uc-06.03.00

Revision 1.2  2007/04/18 22:04:37  tonismar
Geração de arquivos SalodInicial.txt e SaldoMensal.txt

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBSaldoMensal extends Persistente
{
    public function TTPBSaldoMensal()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql .= " SELECT                                                                          \n";
        $stSql .= "      trim(upper(replace(plano_banco.conta_corrente,'-',''))) as conta_corrente  \n";
        $stSql .= "     ,replace(sum(conciliacao.vl_extrato)::varchar,'.',',') as vl_extrato        \n";
        $stSql .= " FROM                                                                            \n";
        $stSql .= "      tesouraria.conciliacao                                                     \n";
        $stSql .= "     ,contabilidade.plano_banco                                                  \n";
        $stSql .= " WHERE                                                                           \n";
        $stSql .= "         conciliacao.cod_plano = plano_banco.cod_plano                           \n";
        $stSql .= "     and conciliacao.exercicio = plano_banco.exercicio                           \n";

        $stSql .= "     and plano_banco.exercicio = '".($this->getDado('exercicio'))."'             \n";
        $stSql .= "     and conciliacao.mes       = ".$this->getDado('inMes')."                     \n";

        $stSql .= "     and plano_banco.cod_entidade in (".$this->getDado('stEntidades').")         \n";
        $stSql .= " GROUP BY                                                                        \n";
        $stSql .= "     plano_banco.conta_corrente                                                  \n";
        $stSql .= " ORDER BY                                                                        \n";
        $stSql .= "     plano_banco.conta_corrente                                                  \n";

        return $stSql;
    }
}

?>