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

class TAdministracaoAssinatura extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TAdministracaoAssinatura()
    {
        parent::Persistente();
        $this->setTabela('ADMINISTRACAO.assinatura');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,numcgm,timestamp');

        $this->AddCampo('exercicio', 'char', true, '4', true, 'TOrcamentoEntidade');
        $this->AddCampo('cod_entidade', 'integer', true, '', true, 'TOrcamentoEntidade');
        $this->AddCampo('numcgm', 'integer', true, '', true, 'TCGM');
        $this->AddCampo('timestamp', 'timestamp', false, '', true, false);
        $this->AddCampo('cargo', 'varchar', true, '30', false, false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSQL = "
select assinatura.exercicio
      ,assinatura.cod_entidade
      ,assinatura.numcgm
      ,sw_cgm.nom_cgm
      ,assinatura.timestamp
      ,assinatura.cargo
      ,assinatura_crc.insc_crc
from administracao.assinatura
join sw_cgm
using (numcgm)
left join administracao.assinatura_crc
using (exercicio,cod_entidade,numcgm,timestamp)
where assinatura.exercicio = '".Sessao::getExercicio()."'
  and assinatura.timestamp = (select max(timestamp) from  administracao.assinatura where exercicio = '".Sessao::getExercicio()."')
        ";

        return $stSQL;
    }

} // end of class
