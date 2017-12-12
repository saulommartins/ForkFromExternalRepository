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
    * Classe de mapeamento da tabela de Configuração
    * Data de Criação: 30/06/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Autor: $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00

*/

/*
$Log$
Revision 1.3  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.2  2007/09/27 12:53:57  hboaventura
adicionando arquivos

Revision 1.1  2007/09/25 21:44:59  hboaventura
adicionando arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");

class TTBAConfiguracao extends TAdministracaoConfiguracaoEntidade
{
    public function TTBAConfiguracao()
    {
        parent::TAdministracaoConfiguracaoEntidade();

        $this->SetDado("exercicio",Sessao::getExercicio());
        $this->SetDado("cod_modulo",45);
    }

    public function recuperaUnidadeGestoraEntidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaUnidadeGestoraEntidade",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaUnidadeGestoraEntidade()
    {
        $stSql = "
            SELECT  entidade.cod_entidade
                 ,  entidade.exercicio
                 ,  sw_cgm.nom_cgm as nom_entidade
                 ,  configuracao_entidade.valor as cod_unidade_gestora
              FROM  orcamento.entidade
        INNER JOIN  sw_cgm
                ON  sw_cgm.numcgm = entidade.numcgm
         LEFT JOIN  administracao.configuracao_entidade
                ON  configuracao_entidade.cod_entidade = entidade.cod_entidade
               AND  configuracao_entidade.exercicio = entidade.exercicio
               AND  configuracao_entidade.cod_modulo = 45
             WHERE  entidade.exercicio = '".$this->getDado('exercicio')."'
        ";
        if ( $this->getDado('cod_entidade') ) {
            $stSql .= " AND entidade.cod_entidade = ".$this->getDado('cod_entidade')." ";
        }
        $stSql .= "
          ORDER BY  entidade.cod_entidade
        ";

        return $stSql;
    }

}
