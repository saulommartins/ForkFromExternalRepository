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
    * Classe de mapeamento da tabela ADMINISTRACAO.TIPO_DOCUMENTO
    * Data de Criação: 26/09/2006

    * @author Analista:
    * @author Desenvolvedor: Leandro Zis

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-01.01.08
*/

/*
$Log$
Revision 1.1  2007/05/17 19:56:30  leandro.zis
uc - 01.01.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TAdministracaoAssinaturaModulo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TAdministracaoAssinaturaModulo()
    {
        parent::Persistente();
        $this->setTabela('ADMINISTRACAO.assinatura_modulo');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,numcgm,timestamp,cod_modulo');

        $this->AddCampo('exercicio', 'char', true, '4', true, 'TAdministracaoAssinatura');
        $this->AddCampo('cod_entidade', 'integer', true, '', true, 'TAdministracaoAssinatura');
        $this->AddCampo('numcgm', 'integer', true, '', true, 'TAdministracaoAssinatura');
        $this->AddCampo('timestamp', 'timestamp', true, '', true, 'TAdministracaoAssinatura');
        $this->AddCampo('cod_modulo', 'integer', true, '', true, true);
    }

    public function montaRecuperaModulosPorAssinatura()
    {
        $stSQL = "
        select assinatura_modulo.cod_modulo
        from administracao.assinatura_modulo
        where assinatura_modulo.exercicio = '".Sessao::getExercicio()."'
        and assinatura_modulo.cod_entidade = ".$this->getDado('cod_entidade')."
        and assinatura_modulo.numcgm = " . $this->getDado('numcgm') . "
          and assinatura_modulo.timestamp = '" . $this->getDado('timestamp') . "' ";

        return $stSQL;
    }

    public function montaRecuperaNumCgmPorModulo()
    {
        $stSql = "
        select distinct assinatura_modulo.numcgm
        from administracao.assinatura_modulo
        where assinatura_modulo.exercicio = '" . Sessao::getExercicio() . "' ";
        $stSql .= " and assinatura_modulo.cod_modulo = " . $this->getDado('cod_modulo') . "  ";
        if ( $this->getDado('cod_entidade') > 0 ) {
            $stSql .= " and assinatura_modulo.cod_entidade = " . $this->getDado('cod_entidade') . " ";
        }
        $stSql .= "and assinatura_modulo.timestamp = (select max(timestamp) from administracao.assinatura_modulo where exercicio = '" . Sessao::getExercicio() . "')";

        return $stSql;
    }

    public function recuperaModulosPorAssinatura(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaModulosPorAssinatura",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function recuperaNumCgmPorModulo(&$rsRecordSet, $stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera( "montaRecuperaNumCgmPorModulo", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

} // end of class
