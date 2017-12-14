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
 * Classe de Visão Relatorio
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santo>
 * @package gestaoFinanceira
 * @subpackage LDO
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

include_once CAM_GF_LDO_CLASSES . 'util/LDOAnotacoes.class.php';
include_once CAM_GF_LDO_CLASSES . 'excecao/LDOExcecao.class.php';

/**
 * Interface Visao Relatorio
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package gestaoFinanceira
 * @subpackage LDO
 */
interface IVLDORelatorio
{
    public function emitir(array $arParametros);

    public function gerar(array $arParametros);
}

/**
 * Classe Abstrata Visao Relatorio
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Pedro Vaz de Mello de Medeiros <pedro.medeiros>
 * @package gestaoFinanceira
 * @subpackage LDO
 */
abstract class VLDORelatorio extends LDOAnotacoes
{
    private static $boInterno = false;
    private static $arInstancia = NULL;
    private $obRegra = NULL;

    final public function __construct()
    {
        # Obtem o nome da classe filha.
        $obVisao = new ReflectionClass($this);

        # validar métodos da classe filha.
        parent::validarClasse($obVisao->getName());

        if (!self::$boInterno) {
            throw new VLDOExcecao("Construtor é privado, use 'recuperarInstancia'", $this->recuperarAnotacoes());
        }

        if (method_exists($this, 'inicializar'))
            $this->inicializar();
    }

    /**
     * Função para obter Singleton da classe filha.
     * @param $stClasse nome da classe filha a usar para criar instância
     * @return objeto da Visão do LDO derivado
     */
    public static function recuperarInstancia($stClasse)
    {
        self::$boInterno = true;

        if (!self::$arInstancia[$stClasse]) {
            self::$arInstancia[$stClasse] = new $stClasse;
        }

        self::$boInterno = false;

        return self::$arInstancia[$stClasse];
    }

    /**
     * Executa ação recebida na página de processamento (PR).
     * @param $arParametros array REQUEST
     */
    public function executarAcao(array $arParametros)
    {
        $stMetodo = $arParametros['stAcao'];

        if (is_string($stMetodo)) {
            echo $this->$stMetodo($arParametros);
        } else {
            throw new VLDOExcecao('comando não encontrado', $this->recuperarAnotacoes());
        }
    }

    /**
     * Executa ação recebida na página oculta (OC).
     * @param $arParametros array REQUEST
     */
    public function executarControle(array $arParametros)
    {
        Sessao::setTrataExcecao( true );

        $stMetodo = $arParametros['stCtrl'];

        if (is_string($stMetodo)) {
            echo $this->$stMetodo($arParametros);
        }

        Sessao::encerraExcecao();
    }
}
